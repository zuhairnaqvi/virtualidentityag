<?php
/*
 * This file is part of the Virtual-Identity Social Media Aggregator package.
 *
 * (c) Virtual-Identity <development@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\AggregatorBundle\Interfaces;

/**
 * UnifiedSocialEntityInterface
 *
 * Your own implementation of the UnifiedSocialEntity must extend this interface
 */
interface UnifiedSocialEntityInterface
{

    public function getId();
    public function setType($type);
    public function getType();
    public function setForeignKey($foreignKey);
    public function getForeignKey();
    public function setCreated($created);
    public function getCreated();
    public function setText($text);
    public function getText();
    public function setImageUrl($imageUrl);
    public function getImageUrl();
    public function setVideoUrl($videoUrl);
    public function getVideoUrl();
    public function setLinkUrl($linkUrl);
    public function getLinkUrl();
    public function setProfileImageUrl($profileImageUrl);
    public function getProfileImageUrl();
    public function setApproved($approved);
    public function getApproved();
}
