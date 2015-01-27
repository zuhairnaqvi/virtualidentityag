<?php
/*
 * This file is part of the Virtual-Identity Facebook package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\FacebookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use VirtualIdentity\FacebookBundle\Interfaces\FacebookEntityInterface;

/**
 * FacebookEntity
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FacebookEntity implements FacebookEntityInterface
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
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean")
     */
    protected $approved;

    /**
     * @var string
     *
     * @ORM\Column(name="raw", type="text")
     */
    protected $raw;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="text")
     */
    protected $facebookId;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    protected $message;

    /**
     * @var string
     *
     * @ORM\Column(name="story", type="text", nullable=true)
     */
    protected $story;

    /**
     * @var string
     *
     * @ORM\Column(name="fromId", type="text", length=255)
     */
    protected $fromId;

    /**
     * @var string
     *
     * @ORM\Column(name="fromName", type="text", length=255)
     */
    protected $fromName;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="text", length=255, nullable=true)
     */
    protected $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="text", length=255, nullable=true)
     */
    protected $link;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="text", length=255, nullable=true)
     */
    protected $source;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text", length=255)
     */
    protected $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdTime", type="datetime")
     */
    protected $createdTime;

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

    /**
     * Get raw
     *
     * @return string
     */
    public function getRaw() {
        return $this->raw;
    }

    /**
     * Set raw
     *
     * @param string $raw
     * @return
     */
    public function setRaw($raw) {
        $this->raw = $raw;
    }

    /**
     * Sets the value of id.
     *
     * @param integer $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        if (is_string($id)) {
            return $this->setFacebookId($id);
        }
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of facebookId.
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Sets the value of facebookId.
     *
     * @param string $facebookId the facebookId
     *
     * @return self
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Gets the value of message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the value of message.
     *
     * @param string $message the message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Gets the value of fromId.
     *
     * @return string
     */
    public function getFromId()
    {
        return $this->fromId;
    }

    /**
     * Sets the value of fromId.
     *
     * @param string $fromId the fromId
     *
     * @return self
     */
    public function setFromId($fromId)
    {
        $this->fromId = $fromId;

        return $this;
    }

    /**
     * Gets the value of fromName.
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * Sets the value of fromName.
     *
     * @param string $fromName the fromName
     *
     * @return self
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;

        return $this;
    }

    /**
     * Gets the value of picture.
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Sets the value of picture.
     *
     * @param string $picture the picture
     *
     * @return self
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Gets the value of createdTime.
     *
     * @return \DateTime
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    /**
     * Sets the value of createdTime.
     *
     * @param \DateTime $createdTime the createdTime
     *
     * @return self
     */
    public function setCreatedTime(\DateTime $createdTime)
    {
        $this->createdTime = $createdTime;

        return $this;
    }

    /**
     * Gets the value of type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the value of type.
     *
     * @param string $type the type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets the value of story.
     *
     * @return string
     */
    public function getStory()
    {
        return $this->story;
    }

    /**
     * Sets the value of story.
     *
     * @param string $story the story
     *
     * @return self
     */
    public function setStory($story)
    {
        $this->story = $story;

        return $this;
    }

    /**
     * Gets the value of link.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Sets the value of link.
     *
     * @param string $link the link
     *
     * @return self
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Gets the value of source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Sets the value of source.
     *
     * @param string $source the source
     *
     * @return self
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }
}
