<?php
/*
 * This file is part of the Virtual-Identity Youtube package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\YoutubeBundle\Form;

/**
 * This entity is used by the formbuilder for the nice userinterface for
 * configuring this bundle via interface
 */
class ConfigurationEntity
{
    /**
     * Youtube-Api-Requests that will be used to retrieve the youtubes
     * @var array
     */
    protected $apiRequests;

    /**
     * Youtubes consumer_key for authentication via OAuth1A
     * @var string
     */
    protected $consumerKey;

    /**
     * Youtubes consumer_secret for authentication via OAuth1A
     * @var string
     */
    protected $consumerSecret;

    /**
     * Youtubes OAuth token for authorization via OAuth1A
     * @var string
     */
    protected $token;

    /**
     * Youtubes OAuth token secret for authorization via OAuth1A
     * @var string
     */
    protected $secret;

    /**
     * Gets the Youtube-Api-Requests that will be used to retrieve the youtubes.
     *
     * @return array
     */
    public function getApiRequests()
    {
        return $this->apiRequests;
    }

    /**
     * Sets the Youtube-Api-Requests that will be used to retrieve the youtubes.
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
     * Gets the Youtubes consumer_key for authentication via OAuth1A.
     *
     * @return string
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * Sets the Youtubes consumer_key for authentication via OAuth1A.
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
     * Gets the Youtubes consumer_secret for authentication via OAuth1A.
     *
     * @return string
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * Sets the Youtubes consumer_secret for authentication via OAuth1A.
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
     * Gets the Youtubes OAuth token for authorization via OAuth1A.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the Youtubes OAuth token for authorization via OAuth1A.
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
     * Gets the Youtubes OAuth token secret for authorization via OAuth1A.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Sets the Youtubes OAuth token secret for authorization via OAuth1A.
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