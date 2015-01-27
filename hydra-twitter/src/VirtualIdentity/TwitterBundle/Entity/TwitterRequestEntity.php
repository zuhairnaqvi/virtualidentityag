<?php
/*
 * This file is part of the Virtual-Identity Twitter package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\TwitterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TwitterRequest
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity("id")
 */
class TwitterRequestEntity
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="string")
     * @Assert\NotBlank()
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string")
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(name="mappedEntity", type="string")
     */
    protected $mappedEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="refreshLifeTime", type="integer")
     */
    protected $refreshLifeTime;

    /**
     * @var int
     *
     * @ORM\Column(name="lastExecutionTime", type="integer", nullable=true)
     */
    protected $lastExecutionTime;

    /**
     * @var float
     *
     * @ORM\Column(name="lastExecutionDuration", type="float", nullable=true)
     */
    protected $lastExecutionDuration;

    /**
     * @var string
     *
     * @ORM\Column(name="orderField", type="string")
     */
    protected $orderField;

    /**
     * @var int
     *
     * @ORM\Column(name="lastMaxId", type="bigint", nullable=true)
     */
    protected $lastMaxId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="useSinceId", type="boolean")
     */
    protected $useSinceId;

    /**
     * Get lastExecutionTime
     *
     * @return int
     */
    public function getLastExecutionTime() {
        return $this->lastExecutionTime;
    }

    /**
     * Set lastExecutionTime
     *
     * @param int $lastExecutionTime
     * @return
     */
    public function setLastExecutionTime($lastExecutionTime) {
        $this->lastExecutionTime = $lastExecutionTime;
    }

    /**
     * Get useSinceId
     *
     * @return boolean
     */
    public function getUseSinceId() {
        return $this->useSinceId;
    }

    /**
     * Set useSinceId
     *
     * @param boolean $useSinceId
     * @return
     */
    public function setUseSinceId($useSinceId) {
        $this->useSinceId = $useSinceId;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     * @return
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Get lastMaxId
     *
     * @return int
     */
    public function getLastMaxId() {
        return $this->lastMaxId;
    }

    /**
     * Set lastMaxId
     *
     * @param int $lastMaxId
     * @return
     */
    public function setLastMaxId($lastMaxId) {
        $this->lastMaxId = $lastMaxId;
    }

    /**
     * Get orderField
     *
     * @return string
     */
    public function getOrderField() {
        return $this->orderField;
    }

    /**
     * Set orderField
     *
     * @param string $orderField
     * @return
     */
    public function setOrderField($orderField) {
        $this->orderField = $orderField;
    }

    /**
     * Get lastExecutionDuration
     *
     * @return float
     */
    public function getLastExecutionDuration() {
        return $this->lastExecutionDuration;
    }

    /**
     * Set lastExecutionDuration
     *
     * @param float $lastExecutionDuration
     * @return
     */
    public function setLastExecutionDuration($lastExecutionDuration) {
        $this->lastExecutionDuration = $lastExecutionDuration;
    }

    /**
     * Get refreshLifeTime
     *
     * @return int
     */
    public function getRefreshLifeTime() {
        return $this->refreshLifeTime;
    }

    /**
     * Set refreshLifeTime
     *
     * @param int $refreshLifeTime
     * @return
     */
    public function setRefreshLifeTime($refreshLifeTime) {
        $this->refreshLifeTime = $refreshLifeTime;
    }

    /**
     * Get mappedEntity
     *
     * @return string
     */
    public function getMappedEntity() {
        return $this->mappedEntity;
    }

    /**
     * Set mappedEntity
     *
     * @param string $mappedEntity
     * @return
     */
    public function setMappedEntity($mappedEntity) {
        $this->mappedEntity = $mappedEntity;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return
     */
    public function setUrl($url) {
        $this->url = $url;
    }
}