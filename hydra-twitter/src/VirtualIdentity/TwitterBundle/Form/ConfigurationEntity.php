<?php
/*
 * This file is part of the Virtual-Identity Twitter package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\TwitterBundle\Form;

/**
 * This entity is used by the formbuilder for the nice userinterface for
 * configuring this bundle via interface
 */
class ConfigurationEntity
{
    /**
     * Twitter-Api-Requests that will be used to retrieve the tweets
     * @var array
     */
    protected $apiRequests;

    /**
     * Twitters consumer_key for authentication via OAuth1A
     * @var string
     */
    protected $consumerKey;

    /**
     * Twitters consumer_secret for authentication via OAuth1A
     * @var string
     */
    protected $consumerSecret;

    /**
     * Twitters OAuth token for authorization via OAuth1A
     * @var string
     */
    protected $token;

    /**
     * Twitters OAuth token secret for authorization via OAuth1A
     * @var string
     */
    protected $secret;

    /**
     * Gets the Twitter-Api-Requests that will be used to retrieve the tweets.
     *
     * @return array
     */
    public function getApiRequests()
    {
        return $this->apiRequests;
    }

    /**
     * Sets the Twitter-Api-Requests that will be used to retrieve the tweets.
     *
     * @param array $apiRequests the apiRequests
     *
     * @return self
     */
    public function setApiRequests(array $apiRequests)
    {
        $this->apiRequests = $apiRequests;

        return $this;
    }

    /**
     * Gets the Twitters consumer_key for authentication via OAuth1A.
     *
     * @return string
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * Sets the Twitters consumer_key for authentication via OAuth1A.
     *
     * @param string $consumerKey the consumerKey
     *
     * @return self
     */
    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;

        return $this;
    }

    /**
     * Gets the Twitters consumer_secret for authentication via OAuth1A.
     *
     * @return string
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * Sets the Twitters consumer_secret for authentication via OAuth1A.
     *
     * @param string $consumerSecret the consumerSecret
     *
     * @return self
     */
    public function setConsumerSecret($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;

        return $this;
    }

    /**
     * Gets the Twitters OAuth token for authorization via OAuth1A.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the Twitters OAuth token for authorization via OAuth1A.
     *
     * @param string $token the token
     *
     * @return self
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Gets the Twitters OAuth token secret for authorization via OAuth1A.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Sets the Twitters OAuth token secret for authorization via OAuth1A.
     *
     * @param string $secret the secret
     *
     * @return self
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }
}