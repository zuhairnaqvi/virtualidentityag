<?php
/*
 * This file is part of the Virtual-Identity Facebook package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\FacebookBundle\Interfaces;

interface FacebookEntityInterface
{
    public function getId();
    public function setMessage($text);
    public function getMessage();
    public function setStory($story);
    public function getStory();
    public function setLink($link);
    public function getLink();
    public function setSource($source);
    public function getsource();
    public function setType($type);
    public function getType();
    public function setFromId($userId);
    public function getFromId();
    public function setFromName($userName);
    public function getFromName();
    public function setPicture($url);
    public function getPicture();
    public function setCreatedTime(\DateTime $createdTime);
    public function getCreatedTime();
    public function setRaw($raw);
    public function getRaw();
    public function setApproved($approved);
    public function getApproved();
}
