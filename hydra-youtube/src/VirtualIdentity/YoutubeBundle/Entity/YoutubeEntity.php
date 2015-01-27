<?php
/*
 * This file is part of the Virtual-Identity Youtube package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\YoutubeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use VirtualIdentity\YoutubeBundle\Interfaces\YoutubeEntityInterface;

/**
 * YoutubeEntity
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class YoutubeEntity implements YoutubeEntityInterface
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
     * @ORM\Column(name="youtubeId", type="text", length=255)
     */
    protected $youtubeId;

    /**
     * @var string
     *
     * @ORM\Column(name="snippetTitle", type="text", length=255)
     */
    protected $snippetTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="snippetDescription", type="text")
     */
    protected $snippetDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="snippetResourceIdVideoId", type="text", length=255)
     */
    protected $snippetResourceIdVideoId;

    /**
     * @var string
     *
     * @ORM\Column(name="snippetThumbnailsHighUrl", type="text", length=255)
     */
    protected $snippetThumbnailsHighUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="snippetPublishedAt", type="datetime")
     */
    protected $snippetPublishedAt;

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
            return $this->setYoutubeId($id);
        }
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of snippetTitle.
     *
     * @return string
     */
    public function getSnippetTitle()
    {
        return $this->snippetTitle;
    }

    /**
     * Sets the value of snippetTitle.
     *
     * @param string $snippetTitle the snippetTitle
     *
     * @return self
     */
    public function setSnippetTitle($snippetTitle)
    {
        $this->snippetTitle = $snippetTitle;

        return $this;
    }

    /**
     * Gets the value of snippetResourceIdVideoId.
     *
     * @return string
     */
    public function getSnippetResourceIdVideoId()
    {
        return $this->snippetResourceIdVideoId;
    }

    /**
     * Sets the value of snippetResourceIdVideoId.
     *
     * @param string $snippetResourceIdVideoId the snippetResourceIdVideoId
     *
     * @return self
     */
    public function setSnippetResourceIdVideoId($snippetResourceIdVideoId)
    {
        $this->snippetResourceIdVideoId = $snippetResourceIdVideoId;

        return $this;
    }

    /**
     * Gets the value of snippetThumbnailsHighUrl.
     *
     * @return string
     */
    public function getSnippetThumbnailsHighUrl()
    {
        return $this->snippetThumbnailsHighUrl;
    }

    /**
     * Sets the value of snippetThumbnailsHighUrl.
     *
     * @param string $snippetThumbnailsHighUrl the snippetThumbnailsHighUrl
     *
     * @return self
     */
    public function setSnippetThumbnailsHighUrl($snippetThumbnailsHighUrl)
    {
        $this->snippetThumbnailsHighUrl = $snippetThumbnailsHighUrl;

        return $this;
    }

    /**
     * Gets the value of snippetPublishedAt.
     *
     * @return \DateTime
     */
    public function getSnippetPublishedAt()
    {
        return $this->snippetPublishedAt;
    }

    /**
     * Sets the value of snippetPublishedAt.
     *
     * @param \DateTime $snippetPublishedAt the snippetPublishedAt
     *
     * @return self
     */
    public function setSnippetPublishedAt(\DateTime $snippetPublishedAt)
    {
        $this->snippetPublishedAt = $snippetPublishedAt;

        return $this;
    }

    /**
     * Gets the value of youtubeId.
     *
     * @return string
     */
    public function getYoutubeId()
    {
        return $this->youtubeId;
    }

    /**
     * Sets the value of youtubeId.
     *
     * @param string $youtubeId the youtubeId
     *
     * @return self
     */
    public function setYoutubeId($youtubeId)
    {
        $this->youtubeId = $youtubeId;

        return $this;
    }

    /**
     * Gets the value of snippetDescription.
     *
     * @return string
     */
    public function getSnippetDescription()
    {
        return $this->snippetDescription;
    }

    /**
     * Sets the value of snippetDescription.
     *
     * @param string $snippetDescription the snippetDescription
     *
     * @return self
     */
    public function setSnippetDescription($snippetDescription)
    {
        $this->snippetDescription = $snippetDescription;

        return $this;
    }
}
