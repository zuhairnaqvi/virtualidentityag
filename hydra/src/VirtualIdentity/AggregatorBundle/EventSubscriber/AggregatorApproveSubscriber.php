<?php
/*
 * This file is part of the Virtual-Identity Social Media Aggregator package.
 *
 * (c) Virtual-Identity <development@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\AggregatorBundle\EventSubscriber;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;

use VirtualIdentity\AggregatorBundle\Services\AggregatorConverterService;
use VirtualIdentity\TwitterBundle\EventDispatcher\TweetChangedEvent;

class AggregatorApproveSubscriber
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
     * The converter service that maps specialiced social entities to the
     * unified entity
     * @var AggregatorConverterService
     */
    protected $converterService;

    /**
     * The class name (fqcn) is used to persist unified social media entities
     * @var String
     */
    protected $unifiedSocialEntityClass;

    /**
     * Sets which class is used to persist the unified social media entities.
     *
     * @param String $unifiedSocialEntityClass Use fqcn (Full qualified class name) here.
     */
    public function setUnifiedSocialEntityClass($unifiedSocialEntityClass)
    {
        $this->unifiedSocialEntityClass = $unifiedSocialEntityClass;
    }

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
     * Is called with different types of events everytime when a social
     * entity changes its approval status.
     * @param  object $socialEntity can be of
     */
    public function onApprovalChange($event)
    {
        $repository = $this->em->getRepository($this->unifiedSocialEntityClass);
        $unified = null;
        $approved = false;


        if ($event instanceof TweetChangedEvent) {
            $tweet = $event->getTweet();
            $unified = $repository->findOneBy(array(
                'type'          => get_class($tweet),
                'foreignKey'    => $tweet->getId()
            ));
            $approved = $tweet->getApproved();
        }

        if (!is_object($unified)) {
            return;
        }

        $unified->setApproved($approved);
        $this->em->persist($unified);
        $this->em->flush();
    }
}

