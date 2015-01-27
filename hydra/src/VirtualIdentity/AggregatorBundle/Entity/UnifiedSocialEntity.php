<?php
/*
 * This file is part of the Virtual-Identity Social Media Aggregator package.
 *
 * (c) Virtual-Identity <development@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\AggregatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use VirtualIdentity\AggregatorBundle\Interfaces\UnifiedSocialEntityInterface;

/**
 * UnifiedSocialEntity
 *
 * @ORM\Table(options={"charset"="utf8mb4","collate"="utf8mb4_unicode_ci"})
 * @ORM\Entity
 */
class UnifiedSocialEntity implements UnifiedSocialEntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="foreignKey", type="integer")
     */
    private $foreignKey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="imageUrl", type="string", length=255, nullable=true)
     */
    private $imageUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="videoUrl", type="string", length=255, nullable=true)
     */
    private $videoUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="linkUrl", type="string", length=255, nullable=true)
     */
    private $linkUrl;

     /**
     * @var string
     *
     * @ORM\Column(name="profileImageUrl", type="string", length=255, nullable=true)
     */
    private $profileImageUrl;   

    /**
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean", nullable=true)
     */
    protected $approved;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return UnifiedSocialEntity
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set foreignKey
     *
     * @param integer $foreignKey
     * @return UnifiedSocialEntity
     */
    public function setForeignKey($foreignKey)
    {
        $this->foreignKey = $foreignKey;

        return $this;
    }

    /**
     * Get foreignKey
     *
     * @return integer
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }


    /**
     * Set profileImageUrl
     *
     * @param string $profileImageUrl
     * @return UnifiedSocialEntity
     */
    public function setProfileImageUrl($profileImageUrl)
    {
        $this->profileImageUrl = $profileImageUrl;
        return $this;
    }

    /**
     * Get foreignKey
     *
     * @return string
     */
    public function getProfileImageUrl()
    {
        return $this->profileImageUrl;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return UnifiedSocialEntity
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return UnifiedSocialEntity
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     * @return UnifiedSocialEntity
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set videoUrl
     *
     * @param string $videoUrl
     * @return UnifiedSocialEntity
     */
    public function setVideoUrl($videoUrl)
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }

    /**
     * Get videoUrl
     *
     * @return string
     */
    public function getVideoUrl()
    {
        return $this->videoUrl;
    }

    /**
     * Set linkUrl
     *
     * @param string $linkUrl
     * @return UnifiedSocialEntity
     */
    public function setLinkUrl($linkUrl)
    {
        $this->linkUrl = $linkUrl;

        return $this;
    }

    /**
     * Get linkUrl
     *
     * @return string
     */
    public function getLinkUrl()
    {
        return $this->linkUrl;
    }

    /**
     * Get approved
     *
     * @return boolean
     */
    public function getApproved() {
        return $this->approved;
    }

    /**
     * Set approved
     *
     * @param boolean $approved
     * @return
     */
    public function setApproved($approved) {
        $this->approved = $approved;
    }
}
