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

use VirtualIdentity\TwitterBundle\Interfaces\TwitterEntityInterface;

use JMS\Serializer\Annotation as JMSS;

/**
 * TwitterEntity
 *
 * @ORM\Table
 * @ORM\Entity
 * @JMSS\ExclusionPolicy("none")
 */
class TwitterEntity implements TwitterEntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMSS\Exclude
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="idStr", type="string", length=255)
     * @JMSS\Type("string")
     */
    private $idStr;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255)
     * @JMSS\Type("string")
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=255)
     * @JMSS\Type("string")
     */
    private $source;

    /**
     * @var integer
     *
     * @ORM\Column(name="userId", type="integer")
     * @JMSS\Type("integer")
     */
    private $userId;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @JMSS\Type("DateTime<'D M d H:i:s T Y'>")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="entitiesMedia0MediaUrl", type="string", length=255, nullable=true)
     * @JMSS\Type("string")
     * @JMSS\SerializedName("entities_media_0_media_url")
     */
    private $entitiesMedia0MediaUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="userProfileImageUrlHttps", type="string", length=255, nullable=true)
     * @JMSS\Type("string")
     */
    private $userProfileImageUrlHttps;

    /**
     * @var string
     *
     * @ORM\Column(name="userScreenName", type="text", length=255)
     * @JMSS\Type("string")
     */
    protected $userScreenName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean")
     * @JMSS\Type("boolean")
     */
    protected $approved;

    /**
     * @var string
     *
     * @ORM\Column(name="raw", type="text")
     * @JMSS\Type("string")
     */
    protected $raw;

    /**
     * @var string
     *
     * @ORM\Column(name="requestId", type="string")
     * @JMSS\Type("string")
     */
    protected $requestId;

    /**
     * @var \Funnel
     *
     * @ORM\ManyToOne(targetEntity="Tpse\Bundle\SocialContentBundle\Entity\Funnel", inversedBy="tweets")
     * @ORM\JoinColumn(name="funnel_id", referencedColumnName="id")
     */
    protected $funnel;    

    /**
     * @ORM\ManyToMany(targetEntity="Tpse\Bundle\CoreBundle\Entity\Tag", inversedBy="instagrams")
     * @ORM\JoinTable(name="twitter_tags")
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
     * Set idStr
     *
     * @param string $idStr
     * @return TwitterEntity
     */
    public function setIdStr($idStr)
    {
        $this->idStr = $idStr;

        return $this;
    }

    /**
     * Get idStr
     *
     * @return string
     */
    public function getIdStr()
    {
        return $this->idStr;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return TwitterEntity
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
     * Set source
     *
     * @param string $source
     * @return TwitterEntity
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return TwitterEntity
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return TwitterEntity
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set entitiesMedia0MediaUrl
     *
     * @param string $entitiesMedia0MediaUrl
     * @return TwitterEntity
     */
    public function setEntitiesMedia0MediaUrl($entitiesMedia0MediaUrl)
    {
        $this->entitiesMedia0MediaUrl = $entitiesMedia0MediaUrl;

        return $this;
    }

    /**
     * Get entitiesMedia0MediaUrl
     *
     * @return string
     */
    public function getEntitiesMedia0MediaUrl()
    {
        return $this->entitiesMedia0MediaUrl;
    }

     /**
     * Set profileImageUrl
     *
     * @param string $profileImageUrl
     * @return TwitterEntity
     */
    public function setUserProfileImageUrlHttps($userProfileImageUrlHttps)
    {
        $this->userProfileImageUrlHttps = $userProfileImageUrlHttps;

        return $this;
    }

    /**
     * Get profileImageUrl
     *
     * @return string
     */
    public function getUserProfileImageUrlHttps()
    {
        return $this->userProfileImageUrlHttps;
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
     * Get userScreenName
     *
     * @return string
     */
    public function getUserScreenName() {
        return $this->userScreenName;
    }

    /**
     * Set userScreenName
     *
     * @param string $userScreenName
     * @return
     */
    public function setUserScreenName($userScreenName) {
        $this->userScreenName = $userScreenName;
    }

    /**
     * Get requestId
     *
     * @return string
     */
    public function getRequestId() {
        return $this->requestId;
    }

    /**
     * Set requestId
     *
     * @param string $requestId
     * @return
     */
    public function setRequestId($requestId) {
        $this->requestId = $requestId;
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
        return $this->getText();
    }

    public function getMainImage()
    {
        return $this->getEntitiesMedia0MediaUrl();
    }    

    public function getAuthor() {
        return $this->getUserScreenName();
    }        

    public function getTimestamp()
    {
        return $this->getCreatedAt();
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
