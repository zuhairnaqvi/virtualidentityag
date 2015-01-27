<?php
/*
 * This file is part of the Virtual-Identity Twitter package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\TwitterBundle\Services;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use JMS\Serializer\SerializerBuilder;

use VirtualIdentity\TwitterBundle\Entity\TwitterEntity;
use VirtualIdentity\TwitterBundle\Exceptions\ApiException;
use VirtualIdentity\TwitterBundle\EventDispatcher\TweetChangedEvent;

/**
 * TwitterService
 * =================
 *
 * The Twitter Service is one of many services used by the AggregatorBundle.
 * However the TwitterService can be used indepenendly of the Aggregator.
 * It eases iterating over your twitter api-call results.
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
class TwitterService
{
    /**
     * Logger used to log error and debug messages
     * @var Monolog\Logger
     */
    public $logger;

    /**
     * Entity Manager used to persist and load TwitterEntities
     * @var Doctrine\ORM\EntityManager
     */
    public $em;

    /**
     * If new entities should automatically be approved or not
     * @var boolean
     */
    public $autoApprove;

    /**
     * The host used for communicating with the twitter api
     * @var String
     */
    public $host;

    /**
     * The authentication credentials for connecting to the twitter api
     * @var array
     */
    public $authentication;

    /**
     * The QueryBuilder used to query the twitter entities
     * @var Doctrine\ORM\QueryBuilder
     */
    public $qb;

    /**
     * The API-Requests that are used to retrieve the tweets
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
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public $dispatcher;

    /**
     * the serializer used to deserialize the responses from twitter
     * @var JMS\Serializer\SerializerBuilder
     */
    public $serializer;

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

        $this->serializer = SerializerBuilder::create()->build();
        $this->loadApiRequests();
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
     * Sets the host that is used in the API-Requests
     *
     * @param String $host API-Host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Loads the api-requests automatically from the database
     */
    public function loadApiRequests()
    {
        $repository = $this->em->getRepository('\VirtualIdentity\TwitterBundle\Entity\TwitterRequestEntity');

        $this->setApiRequests($repository->findAll());
    }

    /**
     * Sets the api requests used to retrieve the tweets.
     * The list must consist of TwitterRequestEntity-objects.
     *
     * @param TwitterRequestEntity $twitterRequestEntitities the api request
     */
    public function setApiRequests(array $twitterRequestEntitities)
    {
        $this->apiRequests = $twitterRequestEntitities;
    }

    /**
     * Returns the list of currently loaded api requests
     *
     * @return array<TwitterRequestEntity> currently loaded api requests
     */
    public function getApiRequests()
    {
        return $this->apiRequests;
    }

    /**
     * Sets the authentication parameters used to connect to the api.
     * You can obtain those values from your app-oauth page.
     * The URL for this page is like following: https://dev.twitter.com/apps/{appId}/oauth
     *
     * @param String $consumerKey      The twitter consumer key
     * @param String $consumerSecret   The twitter consumer secret
     * @param String $oauthToken       The twitter access token
     * @param String $oauthTokenSecret The twitter access token secret
     */
    public function setAuthentication($consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret)
    {
        $this->authentication = array(
            'consumer_key' => $consumerKey,
            'consumer_secret' => $consumerSecret
        );

        if (!empty($oauthToken)) {
            $this->authentication['token'] = $oauthToken;
        }

        if (!empty($oauthTokenSecret)) {
            $this->authentication['secret'] = $oauthTokenSecret;
        }

        $this->initializeApi();
    }

    /**
     * Sets the approval-status of one tweet. Furthermore it dispatches an event.
     *
     * @param int    $tweetId   the tweet id
     * @param bool   $approved  whether or not the tweet is approved
     * @param string $requestId which request the tweet belongs to
     * @return bool
     */
    public function setApproved($tweetId, $approved, $requestId)
    {
        $found = false;
        foreach ($this->apiRequests as $apiRequest) {
            if ($apiRequest->getId() == $requestId) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new \InvalidArgumentException('The api request with ID '.$requestId.' could not be found!');
        }

        $repository = $this->em->getRepository($apiRequest->getMappedEntity());

        $tweet = $repository->findOneById($tweetId);

        if ($tweet == null) {
            throw new \InvalidArgumentException('The tweet with ID '.$tweetId.' could not be found!');
        }

        $tweet->setApproved($approved);

        $this->em->persist($tweet);
        $this->em->flush();

        $this->dispatcher->dispatch(
            'virtual_identity_twitter.post_approval_change',
            new TweetChangedEvent($tweet)
        );

        return $approved;
    }

    /**
     * Returns the query builder used to query the database where the twitter entities are stored.
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
     * @param bool  $onlyApproved if only approved elements should be returned. default is true.
     * @param int   $limit        how many items should be fetched at maximum
     * @param array $requestIds   which requestIds should be fetched, if left empty all are fetched
     * @return array<TwitterEntityInterface> List of twitter entities
     */
    public function getFeed($onlyApproved = true, $limit = false, array $requestIds = array())
    {
        $result = array();
        foreach ($this->apiRequests as $request) {
            if (count($requestIds) > 0 && !in_array($request->getId(), $requestIds)) {
                continue;
            }

            $this->initializeQueryBuilder($request->getId());

            if ($limit !== false && is_int($limit)) {
                $this->qb->setMaxResults($limit);
            }
            if ($onlyApproved) {
                $this->qb->andWhere('e.approved = true');
            }
            $this->qb
                ->andWhere('e.requestId = :requestId')
                ->setParameter('requestId', $request->getId());

            $result = array_merge($result, $this->qb->getQuery()->getResult());
        }

        return $result;
    }

    /**
     * Iterates all configured api requests, fetches the result and tries to
     * deserialize the response to the mapped entities chosen for the
     * corresponding api request.
     *
     * Notes on your entities:
     * * your entities must implement VirtualIdentity\TwitterBundle\Interfaces\TwitterEntity
     * * the attributes must define the JMS\Serializer\Annotation\Type annotation
     * * the result from twitter is flattened before its deserialized
     *
     * What does the last point mean? This means that if your response has a key
     * $entry['id_str'] the property "idStr" is populated. If the response has a
     * key $entry['user']['screen_name'] the property "userScreenName" is
     * populated. Therefore you can persist whatever information you want to
     * persist from the direct response by using the correct name for the
     * entity-field.
     *
     * Hint (1):
     * If your fields don't get deserialized correctly, make use of the
     * JMS\Serializer\Annotation\SerializedName annotation.
     *
     * Hint (2):
     * If you have special code executed in your setters, you must use the
     * JMS\Serializer\Annotation\AccessType annotation! Per default reflection
     * is used and the properties are set directly!
     *
     * Warning:
     * You may not want that the id is populated by deserializing because you
     * want it to be an incremental value. In this case use a combination of
     * JMS\Serializer\Annotation\ExclusionPolicy and
     * JMS\Serializer\Annotation\Exclude or JMS\Serializer\Annotation\Expose
     * annotations.
     *
     * @param array requestIds which requestIds should be executed
     * @return void
     */
    public function syncDatabase(array $requestIds = array())
    {
        if (!$this->api) {
            throw new ApiException('Api not initialized! Use setAuthentication to implicitly initialize the api.');
        }

        foreach ($this->apiRequests as $twitterRequestEntity) {

            if (count($requestIds) && !in_array($twitterRequestEntity->getId(), $requestIds)) {
                continue;
            }

            if (count($requestIds) == 0
            && $twitterRequestEntity->getLastExecutionTime() + $twitterRequestEntity->getRefreshLifeTime() > time()) {
                // we only execute requests if their lifetime is over
                continue;
            }

            $socialEntityClass = $twitterRequestEntity->getMappedEntity();

            if (!class_exists($socialEntityClass)
            && !in_array(
                'VirtualIdentity\TwitterBundle\Interfaces\TwitterEntityInterface',
                class_implements($socialEntityClass) // instead uf is_subclass_of because of compatibility with php 5.3.6
            )) {
                continue;
            }

            $start = microtime(true);
            $url = $twitterRequestEntity->getUrl();

            $params = array();
            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $params);

            $lastMaxId = $twitterRequestEntity->getLastMaxId();
            if ($twitterRequestEntity->getUseSinceId() && is_numeric($lastMaxId)) {
                $params['since_id'] = $lastMaxId;
            }

            // twitter limits the maximum amount of tweets that can be fetched
            // per api request to 200, so making this configurable for the user
            // seems to make no sense
            $params['count'] = 200;

            $status = $this->api->request(
                'GET',
                $this->api->url($url),
                $params
            );

            if ($status == 200) {

                $response = json_decode($this->api->response['response'], true);
                if (isset($response['statuses'])) {
                    $response = $response['statuses'];
                }
                $repository = $this->em->getRepository($socialEntityClass);

                foreach ($response as $rawTweet) {
                    if (!isset($rawTweet['id_str'])) {
                        throw new ApiException('Tweet could not be recognized! There was no id_str in the entity: '.print_r($rawTweet, 1));
                    }
                    if (!count($repository->findOneByIdStr($rawTweet['id_str']))) {
                        $flattened = self::flatten($rawTweet);
                        $rawData = json_encode($flattened);

                        $twitterEntity = $this->serializer->deserialize($rawData, $socialEntityClass, 'json');

                        $twitterEntity->setRequestId($twitterRequestEntity->getId());
                        $twitterEntity->setRaw(json_encode($rawTweet));
                        $twitterEntity->setApproved($this->autoApprove);

                        $this->em->persist($twitterEntity);

                        if ($rawTweet['id_str'] > $lastMaxId) {
                            $lastMaxId = $rawTweet['id_str'];
                        }
                    } else {
                        continue;
                    }
                }
                $this->em->flush();
            } else {
                $twitterRequestEntity->setLastExecutionDuration(-1);
                $twitterRequestEntity->setLastMaxId($lastMaxId);
                $this->em->persist($twitterRequestEntity);
                $this->em->flush();
                throw new ApiException('Request was unsuccessful! Status code: '.$status.'. Response was: '.$this->api->response['response']);
            }

            $timeTaken = microtime(true) - $start;

            $twitterRequestEntity->setLastExecutionDuration($timeTaken);
            $twitterRequestEntity->setLastMaxId($lastMaxId);
            $twitterRequestEntity->setLastExecutionTime(time());
            $this->em->persist($twitterRequestEntity);
            $this->em->flush();
        }
    }

    /**
     * Gives access to the tmhOauth instance for specialised use of tha twitter api.
     * There already exists a method for gaining an authorization-url.
     *
     * @return \tmhOauth
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Calls the twitter api to create a request token and then generates the
     * authorization url. You should store the returned token and secret in the session.
     * When the user must is redirected to the given callback url you can then
     * obtain the access token with those session-parameters.
     *
     * @param  string $callBackUrl the url where the user should be redirected after authorizing the app
     * @return array               the keys of the return are url, userSessionToken and userSessionSecret
     */
    public function getAuthorizationParameters($callBackUrl)
    {
        // send request for a request token
        $status = $this->api->request('POST', $this->api->url('oauth/request_token', ''), array(
            // pass a variable to set the callback
            'oauth_callback' => $callBackUrl
        ));

        if ($status == 200) {

            // get and store the request token
            $response = $this->api->extract_params($this->api->response['response']);

            // generate redirection url the user needs to be redirected to
            $return = array(
                'url' => $this->api->url('oauth/authorize', '') . '?oauth_token=' . $response['oauth_token'],
                'userSessionToken' => $response['oauth_token'],
                'userSessionSecret' => $response['oauth_token_secret'],
            );

            return $return;
        }

        throw new ApiException('Obtaining an request token did not work! Status code: '.$status.'. Response was: '.$this->api->response['response']);
    }

    /**
     * Returns an array with the permanent access token and access secret
     *
     * @param  string $userSessionToken  the current users session token
     * @param  string $userSessionSecret the current users session secret
     * @param  string $oauthVerifier     verifier that was sent to the callback url
     * @return array                     the keys of the returned array are accessToken and accessTokenSecret
     */
    public function getAccessToken($userSessionToken, $userSessionSecret, $oauthVerifier)
    {
        // set the request token and secret we have stored
        $this->api->config['user_token'] = $userSessionToken;
        $this->api->config['user_secret'] = $userSessionSecret;

        // send request for an access token
        $status = $this->api->request('POST', $this->api->url('oauth/access_token', ''), array(
            // pass the oauth_verifier received from Twitter
            'oauth_verifier' => $oauthVerifier
        ));

        if ($status == 200) {

            // get the access token and store it in a cookie
            $response = $this->api->extract_params($this->api->response['response']);

            $return = array(
                'accessToken' => $response['oauth_token'],
                'accessTokenSecret' => $response['oauth_token_secret'],
            );

            return $return;
        }

        throw new ApiException('Obtaining the acecss token did not work! Status code: '.$status.'. Last error message was: '.$this->api->response['error']);
    }

    /**
     * Checks if the authentication credentials currently stored in hydra.yml are correct or not.
     *
     * @return boolean
     */
    public function isAccessTokenValid()
    {
        $this->api->request('GET', $this->api->url('1.1/account/verify_credentials'));

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
     *
     * @param string $requestId the request id for which the builder is initialized
     */
    public function initializeQueryBuilder($requestId)
    {
        $found = false;
        foreach ($this->apiRequests as $apiRequest) {
            if ($apiRequest->getId() == $requestId) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new \InvalidArgumentException('The api request with ID '.$requestId.' could not be found!');
        }

        $this->qb = $this->em->createQueryBuilder();
        $this->qb
                ->select('e')
                ->from($apiRequest->getMappedEntity(), 'e')
                ->orderBy('e.' . $apiRequest->getOrderField(), 'DESC');
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