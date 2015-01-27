<?php
/*
 * This file is part of the Virtual-Identity Facebook package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\FacebookBundle\Form;

/**
 * This entity is used by the formbuilder for the nice userinterface for
 * configuring this bundle via interface
 */
class ConfigurationEntity
{
    /**
     * Facebook-Api-Requests that will be used to retrieve the facebooks
     * @var array
     */
    protected $apiRequests;

    /**
     * Facebooks app_id for authentication via OAuth1A
     * @var string
     */
    protected $appId;

    /**
     * Facebooks app_secret for authentication via OAuth1A
     * @var string
     */
    protected $appSecret;

    /**
     * Facebooks OAuth token for authorization via OAuth1A
     * @var string
     */
    protected $token;

    /**
     * Facebooks OAuth token secret for authorization via OAuth1A
     * @var string
     */
    protected $secret;

    /**
     * Which permissions to ask the user for
     * @var string
     */
    protected $permissions;

    /**
     * Gets the Facebook-Api-Requests that will be used to retrieve the facebooks.
     *
     * @return array
     */
    public function getApiRequests()
    {
        return $this->apiRequests;
    }

    /**
     * Sets the Facebook-Api-Requests that will be used to retrieve the facebooks.
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
     * Gets the Facebooks app_id for authentication via OAuth1A.
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Sets the Facebooks app_id for authentication via OAuth1A.
     *
     * @param string $appId the appId
     *
     * @return self
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * Gets the Facebooks app_secret for authentication via OAuth1A.
     *
     * @return string
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * Sets the Facebooks app_secret for authentication via OAuth1A.
     *
     * @param string $appSecret the appSecret
     *
     * @return self
     */
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;

        return $this;
    }

    /**
     * Gets the Facebooks OAuth token for authorization via OAuth1A.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the Facebooks OAuth token for authorization via OAuth1A.
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
     * Gets the Facebooks OAuth token secret for authorization via OAuth1A.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Sets the Facebooks OAuth token secret for authorization via OAuth1A.
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

    /**
     * Gets the Which permissions to ask the user for.
     *
     * @return string
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Sets the Which permissions to ask the user for.
     *
     * @param string $permissions the permissions
     *
     * @return self
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }
}