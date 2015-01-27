<?php
/*
 * This file is part of the Virtual-Identity Instagram package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\InstagramBundle\Services;

use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;

use VirtualIdentity\InstagramBundle\Entity\InstagramEntity;
use VirtualIdentity\InstagramBundle\Exceptions\ApiException;
use VirtualIdentity\InstagramBundle\EventDispatcher\InstagramChangedEvent;

/**
 * InstagramService
 * =================
 *
 * The Instagram Service is one of many services used by the AggregatorBundle.
 * However the InstagramService can be used indepenendly of the Aggregator.
 * It eases iterating over your instagram api-call results.
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
class InstagramService
{
    /**
     * Logger used to log error and debug messages
     * @var Monolog\Logger
     */
    public $logger;

    /**
     * Entity Manager used to persist and load InstagramEntities
     * @var Doctrine\ORM\EntityManager
     */
    public $em;

    /**
     * The class name (fqcn) is used to persist instagram entities
     * @var String
     */
    public $socialEntityClass;

    /**
     * If new entities should automatically be approved or not
     * @var boolean
     */
    public $autoApprove;

    /**
     * The host used for communicating with the instagram api
     * @var String
     */
    public $host;

    /**
     * The authentication credentials for connecting to the instagram api
     * @var array
     */
    public $authentication;

    /**
     * The QueryBuilder used to query the instagram entities
     * @var Doctrine\ORM\QueryBuilder
     */
    public $qb;

    /**
     * The API-Requests that are used to retrieve the instagrams
     * @var array
     */
    public $apiRequests;

    /**
     * The tmhOAuth Api
     * @var \tmhOAuth
     */
    public $api;

    /**
     * an event dispatcher that is used to dispatch certain events, like when the approval status is changed
     * @var [type]
     */
    public $dispatcher;

    /**
     * Creates a new Aggregator Service. The most important methods are the getFeed and syncDatabase methods.
     *
     * @param Logger        $logger debug messages are logged here
     * @param EntityManager $em     persistence manager
     */
    public function __construct(Logger $logger, EntityManager $em, EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * Sets which class is used to persist the instagram entities.
     *
     * @param String $socialEntityClass Use fqcn (Full qualified class name) here.
     */
    public function setSocialEntityClass($socialEntityClass)
    {
        $this->socialEntityClass = $socialEntityClass;
        $this->initializeQueryBuilder();
    }

    /**
     * Sets if new entities should be approved automatically. If set to true, new
     * posts/instagrams/etc will automatically appear in the feed. If set to false, you have
     * to approve them manually using the admin-interface reachable via /hydra/moderate
     *
     * @param boolean $autoApprove if entities should be autoapproved or not
     */
    public function setAutoApprove($autoApprove)
    {
        $this->autoApprove = $autoApprove;
    }

    /**
     * Sets the host that is used in the API-Requests
     *
     *  @param String $host API-Host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Sets the api request used to retrieve the instagrams.
     * At the moment only GET-requests are allowed. For
     * example 1.1/statuses/user_timeline.json
     *
     * @param String $url the api request
     */
    public function setApiRequests(array $urls)
    {
        $this->apiRequests = $urls;
    }

    /**
     * Sets the authentication parameters used to connect to the api.
     * You can obtain those values from your app-oauth page.
     * The URL for this page is like following: https://dev.instagram.com/apps/{appId}/oauth
     *
     * @param String $consumerKey      The instagram consumer key
     * @param String $consumerSecret   The instagram consumer secret
     * @param String $accessToken      The instagram access token
     */
    public function setAuthentication($consumerKey, $consumerSecret, $accessToken)
    {
        $this->authentication = array(
            'consumer_key' => $consumerKey,
            'consumer_secret' => $consumerSecret
        );

        if (!empty($accessToken)) {
            $this->authentication['accessToken'] = $accessToken;
        }

        $this->initializeApi();
    }

    /**
     * Sets the approval-status of one instagram. Furthermore it dispatches an event.
     *
     * @param int  $instagramId  the instagram id
     * @param bool $approved whether or not the instagram is approved
     * @return bool
     */
    public function setApproved($instagramId, $approved)
    {
        $repository = $this->em->getRepository($this->socialEntityClass);

        $instagram = $repository->findOneById($instagramId);

        if ($instagram == null) {
            throw new \InvalidArgumentException('The instagram with ID '.$instagramId.' could not be found!');
        }

        $instagram->setApproved($approved);

        $this->em->persist($instagram);
        $this->em->flush();

        $this->dispatcher->dispatch(
            'virtual_identity_instagram.post_approval_change',
            new InstagramChangedEvent($instagram)
        );

        return $approved;
    }

    /**
     * Returns the query builder used to query the database where the instagram entities are stored.
     * You can change anything you want on it before calling the getFeed-method.
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
     * @param bool $onlyApproved if only approved elements should be returned. default is true.
     * @param int  $limit        how many items should be fetched at maximum
     * @return array<InstagramEntityInterface> List of instagram entities
     */
    public function getFeed($onlyApproved = true, $limit = false)
    {
        if ($limit !== false && is_int($limit)) {
            $this->qb->setMaxResults($limit);
        }
        if ($onlyApproved) {
            $this->qb->andWhere('e.approved = true');
        }
        return $this->qb->getQuery()->getResult();
    }

    /**
     * Syncs the database of instagram entities with the entities of each social channel configured
     * to be looked up by the aggregator
     *
     * @return void
     */
    public function syncDatabase()
    {
        if (!$this->api) {
            throw new ApiException('Api not initialized! Use setAuthentication to implicitly initialize the api.');
        }

        foreach ($this->apiRequests as $url) {
            $params = array();
            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $params);

            $status = $this->api->request(
                'GET',
                $this->api->url($url),
                array_merge($params, array('access_token' => $this->authentication['accessToken']))
            );

            if ($status == 200) {

                $response = json_decode($this->api->response['response'], true);
                if (!isset($response['data'])) {
                    throw new ApiException('Instagram entities could not be detected! No data-entry was returned: '.$this->api->response['response']);
                }
                $response = $response['data'];
                $repository = $this->em->getRepository($this->socialEntityClass);

                foreach ($response as $rawInstagram) {
                    if (!isset($rawInstagram['id'])) {
                        throw new ApiException('Instagram could not be recognized! There was no id in the entity: '.print_r($rawInstagram, 1));
                    }

                    if (!count($repository->findOneByInstagramId($rawInstagram['id']))) {
                        $instagramEntity = $this->deserializeRawObject($rawInstagram, array('created_time'));

                        $instagramEntity->setRaw(json_encode($rawInstagram));
                        $instagramEntity->setApproved($this->autoApprove);

                        $this->em->persist($instagramEntity);
                    } else {
                        continue;
                    }
                }
                $this->em->flush();
            } else {
                throw new ApiException('Request was unsuccessful! Status code: '.$status.'. Response was: '.$this->api->response['response']);
            }
        }
    }

    /**
     * Gives access to the tmhOauth instance for specialised use of tha instagram api.
     * There already exists a method for gaining an authorization-url.
     *
     * @return \tmhOauth
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Calls the instagram api to create a request token and then generates the
     * authorization url. You should store the returned token and secret in the session.
     * When the user must is redirected to the given callback url you can then
     * obtain the access token with those session-parameters.
     *
     * @param  string $callBackUrl the url where the user should be redirected after authorizing the app
     * @return array               the keys of the return are url, userSessionToken and userSessionSecret
     */
    public function getAuthorizationParameters($callBackUrl)
    {
        // generate redirection url the user needs to be redirected to
        $return = array(
            'url' => $this->api->url('oauth/authorize', '').
                '?client_id=' . $this->authentication['consumer_key'].
                '&redirect_uri='.$callBackUrl.
                '&response_type=code'
        );

        return $return;
    }

    /**
     * Returns an array with the permanent access token and access secret
     *
     * @param  string $code the verification code receivied from authorization request
     * @return array        the keys of the returned array are accessToken
     */
    public function getAccessToken($code, $callBackUrl)
    {
        // set the request token and secret we have stored

        // send request for an access token
        $status = $this->api->request('POST', $this->api->url('oauth/access_token', ''), array(
            // pass the code received from Instagram
            'client_id' => $this->authentication['consumer_key'],
            'client_secret' => $this->authentication['consumer_secret'],
            'grant_type' => 'authorization_code',
            'redirect_uri' => $callBackUrl,
            'code' => $code
        ));

        if ($status == 200) {

            // get the access token and store it in a cookie
            $response = json_decode($this->api->response['response'], true);

            $return = array(
                'accessToken' => $response['access_token']
            );

            return $return;
        }

        throw new ApiException('Obtaining the access token did not work! Status code: '.$status.'. Response was: '.$this->api->response['response']);
    }

    /**
     * Checks if the authentication credentials currently stored in hydra.yml are correct or not.
     *
     * @return boolean
     */
    public function isAccessTokenValid()
    {
        if (empty($this->authentication['accessToken'])) {
            return false;
        }
        $this->api->request('GET', $this->api->url('v1/users/self/feed'), array('access_token' => $this->authentication['accessToken']));

        // HTTP 200 means we were successful
        return ($this->api->response['code'] == 200);
    }

    /**
     * Flattens an multidimensional array to a one dimensional one.
     * Keys are preserved in nesting order. The keys are then glued
     * by the second parameter $glue.
     *
     * @param  array  $array The array that should be flattened
     * @param  String $glue  The string that glues the keys together
     * @return array
     */
    public static function flatten($array, $glue = '_') {
        $result = array();
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array));
        foreach ($it as $v) {
            $d = $it->getDepth();
            $breadcrumb = array();
            for ($cd = 0; $cd <= $d; $cd ++) $breadcrumb[] = $it->getSubIterator($cd)->key();

            $result[join($breadcrumb, $glue)] = $v;
        }
        return $result;
    }

    /**
     * Is called by the constructor. Creates an initializes the query builder.
     * The Entity is set, default ordering by date descending is set
     */
    public function initializeQueryBuilder()
    {
        $this->qb = $this->em->createQueryBuilder();
        $this->qb
                ->select('e')
                ->from($this->socialEntityClass, 'e')
                ->orderBy('e.createdTime', 'DESC');
    }

    /**
     * The raw response from the api must be mapped to a correctly typed object.
     * This method does the job flattening the result and by using a GetSetMethodNormalizer.
     * What does that mean? This means that if your response has a key $entry['id_str'] the
     * setter setIdStr($entry['id_str']) is used. If the response has a key
     * $entry['media'][0]['media_url'] the setter setMedia0MediaUrl(..) is used. Therefore
     * you can persist whatever information you want to persist from the direct response
     * by using the correct name for the entity-field (and then also for the setter).
     *
     * @param  object $object     the json decoded object response by the api
     * @param  array  $dateFields fields that should be formatted as datetime object
     * @return InstagramEntityInterface
     */
    public function deserializeRawObject($object, $dateFields = array())
    {
        // flatten object
        $object = self::flatten($object);
        foreach ($dateFields as $df) {
            if (array_key_exists($df, $object)) {
                $object[$df] = \DateTime::createFromFormat('U', $object[$df]);
            }
        }
        $normalizer = new GetSetMethodNormalizer();
        $normalizer->setCamelizedAttributes(array_keys($object));
        return $normalizer->denormalize($object, $this->socialEntityClass);
    }

    /**
     * creates and initializes the api with the host and the authentication parameters
     */
    public function initializeApi()
    {
        $ops = $this->authentication;
        $ops['host'] = $this->host;

        $this->api = new \tmhOAuth($ops);
    }
}