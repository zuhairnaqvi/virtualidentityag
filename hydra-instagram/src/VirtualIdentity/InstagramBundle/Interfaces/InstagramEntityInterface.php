<?php
/*
 * This file is part of the Virtual-Identity Instagram package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\InstagramBundle\Interfaces;

interface InstagramEntityInterface
{
    public function getId();
    public function setCaptionText($text);
    public function getCaptionText();
    public function setUserId($userId);
    public function getUserId();
    public function setUserUsername($userName);
    public function getUserUsername();
    public function getUserProfilePicture();
    public function setUserProfilePicture($userProfilePicture);
    public function setImagesStandardResolutionUrl($url);
    public function getImagesStandardResolutionUrl();
    public function setCreatedTime(\DateTime $createdTime);
    public function getCreatedTime();
    public function setRaw($raw);
    public function getRaw();
    public function setApproved($approved);
    public function getApproved();
}
