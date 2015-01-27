<?php
/*
 * This file is part of the Virtual-Identity Social Media Aggregator package.
 *
 * (c) Virtual-Identity <development@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\AggregatorBundle\Services;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;

use VirtualIdentity\AggregatorBundle\Exceptions\AggregatorException;
use VirtualIdentity\AggregatorBundle\Interfaces\UnifiedSocialEntityInterface;

use VirtualIdentity\TwitterBundle\Interfaces\TwitterEntityInterface;
use VirtualIdentity\InstagramBundle\Interfaces\InstagramEntityInterface;
use VirtualIdentity\FacebookBundle\Interfaces\FacebookEntityInterface;
use VirtualIdentity\YoutubeBundle\Interfaces\YoutubeEntityInterface;

/**
 * AggregatorService
 * =================
 *
 * The Aggregator Service makes it easy to iterate over multiple social media channels.
 * Make sure, that every social media service is configured properly.
 *
 * Probably the most important methods you want to use are:
 * * getFeed
 * * syncDatabase
 *
 * You might want to call the syncDatabase-method using a cron job. This call is then
 * forwarded to all other social media services.
 *
 * TODO: extend documentation
 */
class AggregatorService
{
    /**
     * Logger used to log debug messages
     * @var Monolog\Logger
     */
    protected $logger;

    /**
     * Entity Manager used to persist and load UnifiedSocialEntities
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * The class name (fqcn) is used to persist unified social media entities
     * @var String
     */
    protected $unifiedSocialEntityClass;

    /**
     * The converter service that maps specialiced social entities to the
     * unified entity
     * @var AggregatorConverterService
     */
    protected $converterService;

    /**
     * If new unified entities should automatically be approved or not
     * @var boolean
     */
    protected $autoApprove;

    /**
     * Array of services that are synced and harvested
     * @var array<Object>
     */
    protected $harvestedServices;

    /**
     * The QueryBuilder used to query the unified social entities
     * @var Doctrine\ORM\QueryBuilder
     */
    protected $qb;

    /**
     * Creates a new Aggregator Service. The most important methods are the getFeed and syncDatabase methods.
     *
     * @param Logger                     $logger            debug messages are logged here
     * @param EntityManager              $em                persistence manager
     * @param AggregatorConverterService $converterService  converts original entites to unified ones and vince versa
     */
    public function __construct(Logger $logger, EntityManager $em, AggregatorConverterService $converterService)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->converterService = $converterService;
    }

    /**
     * Is called by the constructor. Creates an initializes the query builder.
     * The Entity is set, default ordering by date descending is set
     */
    private function initializeQueryBuilder()
    {
        $this->qb = $this->em->createQueryBuilder();
        $this->qb
                ->select('e')
                ->from($this->unifiedSocialEntityClass, 'e')
                ->orderBy('e.created', 'DESC');
    }

    /**
     * Sets which class is used to persist the unified social media entities.
     *
     * @param String $unifiedSocialEntityClass Use fqcn (Full qualified class name) here.
     */
    public function setUnifiedSocialEntityClass($unifiedSocialEntityClass)
    {
        $this->unifiedSocialEntityClass = $unifiedSocialEntityClass;

        $this->initializeQueryBuilder();
    }

    /**
     * Sets if new entities should be approved automatically. If set to true, new
     * posts/tweets/etc will automatically appear in the feed. If set to false, you have
     * to approve them manually using the admin-interface reachable via /hydra/moderate
     *
     * @param boolean $autoApprove if entities should be autoapproved or not
     */
    public function setAutoApprove($autoApprove)
    {
        $this->autoApprove = $autoApprove;
    }

    /**
     * Sets which social media services should be harvested and synced. Those services must
     * implement a getFeed and a syncDatabase method.
     *
     * @param array $harvestedServices<Service> list of services that should be harvested and synced
     */
    public function setHarvestedServices(array $harvestedServices)
    {
        $this->harvestedServices = $harvestedServices;
    }

    /**
     * Returns the query builder used to query the database where the unified
     * social media entities are stored. You can change anything you want on
     * it before calling the getFeed-method.
     *
     * @return Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->qb;
    }

    /**
     * Returns the whole aggregated feed. You can limit the list giving any number.
     *
     * @return List<UnifiedSocialEntityInterface>
     */
    public function getFeed($limit = false)
    {
        if ($limit !== false && is_int($limit)) {
            $this->qb->setMaxResults($limit);
        }
        $this->qb->andWhere('e.approved = 1');
        return $this->qb->getQuery()->getResult();
    }

    /**
     * Syncs the database of unified entities with the entities of each social
     * channel configured to be looked up by the aggregator
     *
     * @return void
     */
    public function syncDatabase()
    {
        foreach ($this->harvestedServices as $service) {
            // we use duck typing here instead of interfaces.
            // we try to keep the social media services independend
            // of the AggregatorBundle
            /*
            if (!method_exists($service, 'syncDatabase')) {
                throw new AggregatorException('The service of class '.get_class($service).
                    ' does not have a syncDatabase method and is therefor not usable by '.
                    'the aggregator. Correct your services.yml');
            }
            */
            if (!method_exists($service, 'getFeed')) {
                throw new AggregatorException('The service of class '.get_class($service).
                    ' does not have a getFeed method and is therefor not usable by '.
                    'the aggregator. Correct your services.yml');
            }

            $this->logger->debug('Syncing '.get_class($service));

            // sync database
            // $service->syncDatabase(); - must be made manually this only syncs from table to table

            // fetch the newly synced entities, the target is to sync our unified repo
            $feed = $service->getFeed(false);

            // sync the unified social entity repository with the corresponding services
            $repository = $this->em->getRepository($this->unifiedSocialEntityClass);
            foreach ($feed as $socialEntity) {
                // yeah, type switching, again because we dont want a
                // dependency on the AggregatorBundle
                $unified = null;
                if ($socialEntity instanceof TwitterEntityInterface
                    && !count($repository->findOneBy(array('type' => get_class($socialEntity), 'foreignKey' => $socialEntity->getId())))
                ) {
                    $unified = new $this->unifiedSocialEntityClass();
                    $unified->setApproved($this->autoApprove);
                    $unified->setType(get_class($socialEntity));
                    $this->converterService->mapTwitterEntity($socialEntity, $unified);
                } elseif ($socialEntity instanceof InstagramEntityInterface
                    && !count($repository->findOneBy(array('type' => get_class($socialEntity), 'foreignKey' => $socialEntity->getId())))
                ) {
                    $unified = new $this->unifiedSocialEntityClass();
                    $unified->setApproved($this->autoApprove);
                    $unified->setType(get_class($socialEntity));
                    $this->converterService->mapInstagramEntity($socialEntity, $unified);
                } elseif ($socialEntity instanceof FacebookEntityInterface
                    && !count($repository->findOneBy(array('type' => get_class($socialEntity), 'foreignKey' => $socialEntity->getId())))
                ) {
                    $unified = new $this->unifiedSocialEntityClass();
                    $unified->setApproved($this->autoApprove);
                    $unified->setType(get_class($socialEntity));
                    $this->converterService->mapFacebookEntity($socialEntity, $unified);
                } elseif ($socialEntity instanceof YoutubeEntityInterface
                    && !count($repository->findOneBy(array('type' => get_class($socialEntity), 'foreignKey' => $socialEntity->getId())))
                ) {
                    $unified = new $this->unifiedSocialEntityClass();
                    $unified->setApproved($this->autoApprove);
                    $unified->setType(get_class($socialEntity));
                    $this->converterService->mapYoutubeEntity($socialEntity, $unified);
                }

                if ($unified != null) {
                    $this->em->persist($unified);
                }
            }
            $this->em->flush();
        }
    }


    /**
     * Sets the approval-status of one social entity. Furthermore it updates its connected specialiced entity
     *
     * @param int  $unifiedId  the id of the unified social entity
     * @param bool $approved   whether or not the tweet is approved
     * @return bool
     */
    public function setApproved($unifiedId, $approved)
    {
        $repository = $this->em->getRepository($this->unifiedSocialEntityClass);

        $socialEntity = $repository->findOneById($unifiedId);

        if ($socialEntity == null) {
            throw new \InvalidArgumentException('The unified entity with ID '.$unifiedId.' could not be found!');
        }


        $socialEntity->setApproved($approved);

        $specialicedEntity = $this->converterService->getOriginalEntity($socialEntity);
        $specialicedEntity->setApproved($approved);

        $this->em->persist($socialEntity);
        $this->em->persist($specialicedEntity);

        $this->em->flush();

        return $approved;
    }

}
