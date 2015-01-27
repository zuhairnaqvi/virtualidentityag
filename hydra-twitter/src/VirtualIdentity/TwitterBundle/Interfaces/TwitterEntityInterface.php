<?php
/*
 * This file is part of the Virtual-Identity Twitter package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\TwitterBundle\Interfaces;

interface TwitterEntityInterface
{
    public function getId();
    public function setIdStr($idStr);
    public function getIdStr();
    public function setText($text);
    public function getText();
    public function setSource($source);
    public function getSource();
    public function setUserId($userId);
    public function getUserId();
    public function setUserScreenName($userScreenName);
    public function getUserScreenName();
    public function setEntitiesMedia0MediaUrl($entitiesMediaMediaUrl);
    public function getEntitiesMedia0MediaUrl();
    public function setUserProfileImageUrlHttps($userProfileImageUrlHttps);
    public function getUserProfileImageUrlHttps();
    public function setCreatedAt($createdAt);
    public function getCreatedAt();
    public function setRequestId($requestId);
    public function getRequestId();
    public function setRaw($raw);
    public function getRaw();
    public function setApproved($approved);
    public function getApproved();
}
