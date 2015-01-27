<?php
/*
 * This file is part of the Virtual-Identity Instagram package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\InstagramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use VirtualIdentity\InstagramBundle\Interfaces\InstagramEntityInterface;

/**
 * InstagramEntity
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class InstagramEntity implements InstagramEntityInterface
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
     * @ORM\Column(name="instagramId", type="text")
     */
    protected $instagramId;

    /**
     * @var string
     *
     * @ORM\Column(name="captionText", type="text", nullable=true)
     */
    protected $captionText;

    /**
     * @var string
     *
     * @ORM\Column(name="userId", type="text", length=255)
     */
    protected $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="userUsername", type="text", length=255)
     */
    protected $userUsername;


    /**
     * @var string
     *
     * @ORM\Column(name="userProfilePicture", type="text", length=255, nullable=true)
     */
    protected $userProfilePicture;

    /**
     * @var string
     *
     * @ORM\Column(name="imagesStandardResolutionUrl", type="text", length=255)
     */
    protected $imagesStandardResolutionUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdTime", type="datetime")
     */
    protected $createdTime;

    /**
     * @var \Funnel
     *
     * @ORM\ManyToOne(targetEntity="Tpse\Bundle\SocialContentBundle\Entity\Funnel", inversedBy="instagrams")
     * @ORM\JoinColumn(name="funnel_id", referencedColumnName="id")
     */
    protected $funnel;

    /**
     * @ORM\ManyToMany(targetEntity="Tpse\Bundle\CoreBundle\Entity\Tag", inversedBy="instagrams")
     * @ORM\JoinTable(name="instagram_tags")
     **/
     protected $tags;    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }     

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
            return $this->setInstagramId($id);
        }
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of instagramId.
     *
     * @return string
     */
    public function getInstagramId()
    {
        return $this->instagramId;
    }

    /**
     * Sets the value of instagramId.
     *
     * @param string $instagramId the instagramId
     *
     * @return self
     */
    public function setInstagramId($instagramId)
    {
        $this->instagramId = $instagramId;

        return $this;
    }

    /**
     * Gets the value of captionText.
     *
     * @return string
     */
    public function getCaptionText()
    {
        return $this->captionText;
    }

    /**
     * Sets the value of captionText.
     *
     * @param string $captionText the captionText
     *
     * @return self
     */
    public function setCaptionText($captionText)
    {
        $this->captionText = $captionText;

        return $this;
    }

    /**
     * Gets the value of userId.
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Sets the value of userId.
     *
     * @param string $userId the userId
     *
     * @return self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Gets the value of userUsername.
     *
     * @return string
     */
    public function getUserUsername()
    {
        return $this->userUsername;
    }

    /**
     * Sets the value of userUsername.
     *
     * @param string $userUsername the userUsername
     *
     * @return self
     */
    public function setUserUsername($userUsername)
    {
        $this->userUsername = $userUsername;

        return $this;
    }

    /**
     * Gets the value of profilePicture.
     *
     * @return string
     */
    public function getUserProfilePicture()
    {
        return $this->userProfilePicture;
    }

    /**
     * Sets the value of profilePicture.
     *
     * @param string $profilePicture the profilePicture
     *
     * @return self
     */
    public function setUserProfilePicture($userProfilePicture)
    {
        $this->userProfilePicture = $userProfilePicture;

        return $this;
    }    

    /**
     * Gets the value of imagesStandardResolutionUrl.
     *
     * @return string
     */
    public function getImagesStandardResolutionUrl()
    {
        return $this->imagesStandardResolutionUrl;
    }

    /**
     * Sets the value of imagesStandardResolutionUrl.
     *
     * @param string $imagesStandardResolutionUrl the imagesStandardResolutionUrl
     *
     * @return self
     */
    public function setImagesStandardResolutionUrl($imagesStandardResolutionUrl)
    {
        $this->imagesStandardResolutionUrl = $imagesStandardResolutionUrl;

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
     * Gets the value of funnel.
     *
     * @return \Funnel
     */
    public function getFunnel()
    {
        return $this->funnel;
    }

    /**
     * Sets the value of funnel.
     *
     * @param \Funnel $funnel the funnel
     *
     * @return self
     */
    public function setFunnel(\Tpse\Bundle\SocialContentBundle\Entity\Funnel $funnel)
    {
        $this->funnel = $funnel;

        return $this;
    }

    public function getTitle() {
        return $this->getCaptionText();
    }

    public function getBody() {
        return $this->getUserUsername();
    }    

    public function getMainImage()
    {
        return $this->getImagesStandardResolutionUrl();
    }    

    public function getAuthor() {
        return $this->getUserUsername();
    }    

    public function getTimestamp()
    {
        return $this->getCreatedTime();
    }    

    /**
     * Add tags
     *
     * @param \Tpse\Bundle\CoreBundle\Entity\Tag $tags
     * @return Accommodation
     */
    public function addTag(\Tpse\Bundle\CoreBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Tpse\Bundle\CoreBundle\Entity\Tag $tags
     */
    public function removeTag(\Tpse\Bundle\CoreBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }    
}
