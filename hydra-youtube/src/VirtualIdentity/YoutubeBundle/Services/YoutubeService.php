<?php
/*
 * This file is part of the Virtual-Identity Youtube package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\YoutubeBundle\Services;

use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Yaml\Yaml;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;

use VirtualIdentity\YoutubeBundle\Entity\YoutubeEntity;
use VirtualIdentity\YoutubeBundle\Exceptions\ApiException;
use VirtualIdentity\YoutubeBundle\EventDispatcher\YoutubeChangedEvent;

/**
 * YoutubeService
 * =================
 *
 * The Youtube Service is one of many services used by the AggregatorBundle.
 * However the YoutubeService can be used indepenendly of the Aggregator.
 * It eases iterating over your youtube api-call results.
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
class YoutubeService
{
    /**
     * Logger used to log error and debug messages
     * @var Monolog\Logger
     */
    protected $logger;

    /**
     * Entity Manager used to persist and load YoutubeEntities
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * The class name (fqcn) is used to persist youtube entities
     * @var String
     */
    protected $socialEntityClass;

    /**
     * If new entities should automatically be approved or not
     * @var boolean
     */
    protected $autoApprove;

    /**
     * The host used for communicating with the youtube api
     * @var String
     */
    protected $host;

    /**
     * The authentication credentials for connecting to the youtube api
     * @var array
     */
    protected $authentication;

    /**
     * The QueryBuilder used to query the youtube entities
     * @var Doctrine\ORM\QueryBuilder
     */
    protected $qb;

    /**
     * The API-Requests that are used to retrieve the youtubes
     * @var array
     */
    protected $apiRequests;

    /**
     * The tmhOAuth Api
     * @var \tmhOAuth
     */
    protected $api;

    /**
     * an event dispatcher that is used to dispatch certain events, like when the approval status is changed
     * @var [type]
     */
    protected $dispatcher;

    /**
     * Creates a new Aggregator Service. The most important methods are the getFeed and syncDatabase methods.
     *
     * @param Logger                   $logger     debug messages are logged here
     * @param EntityManager            $em         persistence manager
     * @param EventDispatcherInterface $dispatcher eventdispatcher for dispatching moderation events
     * @param Kernel/HttpKernel        $kernel     environment kernel
     */
    public function __construct(Logger $logger, EntityManager $em, EventDispatcherInterface $dispatcher, $kernel)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->em = $em;
        $this->kernel = $kernel;
    }

    /**
     * Sets which class is used to persist the youtube entities.
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
     * posts/youtubes/etc will automatically appear in the feed. If set to false, you have
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
     * Sets the api request used to retrieve the youtubes.
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
     * The URL for this page is like following: https://dev.youtube.com/apps/{appId}/oauth
     *
     * @param String $consumerKey      The youtube consumer key
     * @param String $consumerSecret   The youtube consumer secret
     * @param String $accessToken      The youtube access token
     */
    public function setAuthentication($consumerKey, $consumerSecret, $accessToken)
    {
        $this->authentication = array(
            'consumer_key' => $consumerKey,
            'consumer_secret' => $consumerSecret
        );

        if (!empty($accessToken)) {
            $this->authentication['bearer'] = $accessToken;
        }

        try {
            $this->refreshAccessToken();
        } catch (ApiException $e) {
            // nothing. this only happens if the configuration was not set correctly
        }
        $this->initializeApi();
    }

    /**
     * Sets the approval-status of one youtube. Furthermore it dispatches an event.
     *
     * @param int  $youtubeId  the youtube id
     * @param bool $approved whether or not the youtube is approved
     * @return bool
     */
    public function setApproved($youtubeId, $approved)
    {
        $repository = $this->em->getRepository($this->socialEntityClass);

        $youtube = $repository->findOneById($youtubeId);

        if ($youtube == null) {
            throw new \InvalidArgumentException('The youtube with ID '.$youtubeId.' could not be found!');
        }

        $youtube->setApproved($approved);

        $this->em->persist($youtube);
        $this->em->flush();

        $this->dispatcher->dispatch(
            'virtual_identity_youtube.post_approval_change',
            new YoutubeChangedEvent($youtube)
        );

        return $approved;
    }

    /**
     * Returns the query builder used to query the database where the youtube entities are stored.
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
     * @return array<YoutubeEntityInterface> List of youtube entities
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
     * Syncs the database of youtube entities with the entities of each social channel configured
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

            $status = $this->api->apponly_request(array(
                'method' => 'GET',
                'url' => $this->api->url($url),
                'params' => $params
            ));

            if ($status == 200) {

                $response = json_decode($this->api->response['response'], true);
                if (!isset($response['data'])) {
                    if (!isset($response['items'])) {
                        throw new ApiException('Youtube entities could not be detected! No data- nor items-entry was returned: '.$this->api->response['response']);
                    } else {
                        $response = $response['items'];
                    }
                } else {
                    $response = $response['data'];
                }
                $repository = $this->em->getRepository($this->socialEntityClass);

                //die(print_r($response));

                foreach ($response as $rawYoutube) {
                    if (!isset($rawYoutube['id'])) {
                        throw new ApiException('Youtube entity could not be recognized! There was no id in the entity: '.print_r($rawYoutube, 1));
                    }
                    if (!count($repository->findOneByYoutubeId($rawYoutube['id']))) {
                        $youtubeEntity = $this->deserializeRawObject($rawYoutube, array('snippet_publishedAt'));

                        $youtubeEntity->setRaw(json_encode($rawYoutube));
                        $youtubeEntity->setApproved($this->autoApprove);

                        $this->em->persist($youtubeEntity);
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
     * Gives access to the tmhOauth instance for specialised use of tha youtube api.
     * There already exists a method for gaining an authorization-url.
     *
     * @return \tmhOauth
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Calls the youtube api to create a request token and then generates the
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
            'url' => 'https://accounts.google.com/o/oauth2/auth'.
                '?client_id=' . $this->authentication['consumer_key'].
                '&redirect_uri='.$callBackUrl.
                '&response_type=code'.
                '&scope=https://www.googleapis.com/auth/youtube.readonly'.
                '&access_type=offline'.
                '&approval_prompt=force'
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
        // send request for an access token
        $status = $this->api->request(
            'POST',
            'https://accounts.google.com/o/oauth2/token',
            array(
                // pass the code received from Youtube
                'client_id' => $this->authentication['consumer_key'],
                'client_secret' => $this->authentication['consumer_secret'],
                'grant_type' => 'authorization_code',
                'redirect_uri' => $callBackUrl,
                'code' => $code
            )
        );

        if ($status == 200) {

            // get the access token and store it in a cookie
            $response = json_decode($this->api->response['response'], true);

            $return = array(
                'accessToken' => $response['access_token'],
                'refreshToken' => $response['refresh_token'],
                'expiresIn' => $response['expires_in']
            );

            return $return;
        }

        throw new ApiException('Obtaining the access token did not work! Status code: '.$status.'. Response was: '.$this->api->response['response']);
    }

    /**
     * refreshes the google access token if the current token is expired.
     * A refresh_token and a expire_date must be set in the config.
     *
     * @return string the new access token
     */
    public function refreshAccessToken()
    {
        $hydraConfigFile = $this->kernel->getRootDir().'/config/hydra.yml';

        if (!file_exists($hydraConfigFile)) {
            return '';
        }

        $hydraConfig = Yaml::parse(file_get_contents($hydraConfigFile));

        if (!isset($hydraConfig['virtual_identity_youtube']['refresh_token'])
        || !isset($hydraConfig['virtual_identity_youtube']['consumer_key'])
        || !isset($hydraConfig['virtual_identity_youtube']['consumer_secret'])
        || !isset($hydraConfig['virtual_identity_youtube']['expire_date'])
        ) {
            throw new ApiException('Refreshing the access token did not work, '.
                'because either no consumer_key, consumer_secret, expire_date or '.
                'refresh_token was found in app/config/hydra.yml');
        }

        if (time() < $hydraConfig['virtual_identity_youtube']['expire_date']) {
            return $hydraConfig['virtual_identity_youtube']['token'];
        }

        // send request for an access token
        $api = new \tmhOAuth();
        $status = $api->apponly_request(array(
            'method' => 'POST',
            'url' => 'https://accounts.google.com/o/oauth2/token',
            'params' => array(
                // pass the code received from Youtube
                'client_id' => $this->authentication['consumer_key'],
                'client_secret' => $this->authentication['consumer_secret'],
                'grant_type' => 'refresh_token',
                'refresh_token' => $hydraConfig['virtual_identity_youtube']['refresh_token']
            )
        ));

        if ($status == 200) {

            // get the access token and store it in a cookie
            $response = json_decode($api->response['response'], true);

            $return = array(
                'accessToken' => $response['access_token'],
                'expiresIn' => $response['expires_in']
            );

        } else {
            throw new ApiException('Obtaining the refreshed access token did not work! Status code: '.$status.'. Response was: '.$api->response['response']);
        }


        $expireDate = time() + $return['expiresIn'] - 10;

        $hydraConfig['virtual_identity_youtube']['token']         = $return['accessToken'];
        $hydraConfig['virtual_identity_youtube']['expire_date']   = $expireDate;

        // save changes (must save first, so no endless loop happens)
        file_put_contents($hydraConfigFile, Yaml::dump($hydraConfig, 3));

        // update runtime configuration
        $this->setAuthentication(
            $hydraConfig['virtual_identity_youtube']['consumer_key'],
            $hydraConfig['virtual_identity_youtube']['consumer_secret'],
            $return['accessToken']
        );

        // clear cache
        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($this->kernel);
        $application->setAutoExit(false);
        $options = array('command' => 'cache:clear');
        $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));

        return $return['accessToken'];
    }

    /**
     * Checks if the authentication credentials currently stored in hydra.yml are correct or not.
     *
     * @return boolean
     */
    public function isAccessTokenValid()
    {
        if (empty($this->authentication['bearer'])) {
            return false;
        }
        $this->api->apponly_request(array(
            'method' => 'GET',
            'url' => $this->api->url('youtube/v3/activities', ''),
            'params' => array(
                'part' => 'id',
                'mine' => 'true'
            )
        ));

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
    protected function initializeQueryBuilder()
    {
        $this->qb = $this->em->createQueryBuilder();
        $this->qb
                ->select('e')
                ->from($this->socialEntityClass, 'e')
                ->orderBy('e.snippetPublishedAt', 'DESC');
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
     * @return YoutubeEntityInterface
     */
    protected function deserializeRawObject($object, $dateFields = array())
    {
        // flatten object
        $object = self::flatten($object);
        foreach ($dateFields as $df) {
            if (array_key_exists($df, $object)) {
                $object[$df] = new \DateTime($object[$df]);
            }
        }
        $normalizer = new GetSetMethodNormalizer();
        $normalizer->setCamelizedAttributes(array_keys($object));
        return $normalizer->denormalize($object, $this->socialEntityClass);
    }

    /**
     * creates and initializes the api with the host and the authentication parameters
     */
    protected function initializeApi()
    {
        $ops = $this->authentication;
        $ops['host'] = $this->host;

        $this->api = new \tmhOAuth($ops);
    }
}