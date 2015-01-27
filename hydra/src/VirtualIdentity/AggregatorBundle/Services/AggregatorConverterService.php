<?php
/*
 * This file is part of the Virtual-Identity Social Media Aggregator package.
 *
 * (c) Virtual-Identity <development@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\AggregatorBundle\Services;

use Monolog\Logger;
use Doctrine\ORM\EntityManager;

use VirtualIdentity\AggregatorBundle\Exceptions\AggregatorException;
use \VirtualIdentity\TwitterBundle\Interfaces\TwitterEntityInterface;
use \VirtualIdentity\AggregatorBundle\Interfaces\UnifiedSocialEntityInterface;
use \VirtualIdentity\InstagramBundle\Interfaces\InstagramEntityInterface;
use \VirtualIdentity\FacebookBundle\Interfaces\FacebookEntityInterface;
use \VirtualIdentity\YoutubeBundle\Interfaces\YoutubeEntityInterface;

/**
 * AggregatorService
 * =================
 *
 * The Aggregator Service makes it easy to iterate over multiple social media channels.
 * Make sure, that every social media service is configured properly.
 *
 * Probably the most important methods you want to use are:
 * * getFeed
 * * syncDatabase
 *
 * You might want to call the syncDatabase-method using a cron job. This call is then
 * forwarded to all other social media services.
 *
 * TODO: extend documentation
 */
class AggregatorConverterService
{
    /**
     * Logger used to log debug messages
     * @var Monolog\Logger
     */
    protected $logger;

    /**
     * Entity Manager used to load specialiced entities
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Creates a new Aggregator Converter Service.
     * Look downwards for the corresponding converter methods you may
     * want to override.
     *
     * @param Logger $logger debug messages are logged here
     */
    public function __construct(Logger $logger, EntityManager $em)
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * converts a tweet to a unified entity
     *
     * @param  TwitterEntityInterface       $socialEntity the tweet object
     * @param  UnifiedSocialEntityInterface $unified      the unified object
     * @return void
     */
    public function mapTwitterEntity(TwitterEntityInterface $socialEntity, UnifiedSocialEntityInterface $unified)
    {
        $unified->setForeignKey($socialEntity->getId());
        $unified->setCreated($socialEntity->getCreatedAt());
        $unified->setText($socialEntity->getText());
        $unified->setProfileImageUrl($socialEntity->getUserProfileImageUrlHttps());
        if ($socialEntity->getEntitiesMedia0MediaUrl()) {
            $unified->setImageUrl($socialEntity->getEntitiesMedia0MediaUrl());
        }
    }

    /**
     * converts a instagram to a unified entity
     *
     * @param  InstagramEntityInterface     $socialEntity the instagram object
     * @param  UnifiedSocialEntityInterface $unified      the unified object
     * @return void
     */
    public function mapInstagramEntity(InstagramEntityInterface $socialEntity, UnifiedSocialEntityInterface $unified)
    {
        $caption = $socialEntity->getCaptionText();

        $unified->setForeignKey($socialEntity->getId());
        $unified->setCreated($socialEntity->getCreatedTime());
        $unified->setText($caption === null ? '' : $caption);
        $unified->setImageUrl($socialEntity->getImagesStandardResolutionUrl());
        $unified->setProfileImageUrl($socialEntity->getUserProfilePicture());
    }

    /**
     * converts a youtube item to a unified entity
     *
     * @param  YoutubeEntityInterface       $socialEntity the youtube object
     * @param  UnifiedSocialEntityInterface $unified      the unified object
     * @return void
     */
    public function mapYoutubeEntity(YoutubeEntityInterface $socialEntity, UnifiedSocialEntityInterface $unified)
    {
        $text = $socialEntity->getSnippetTitle();

        $unified->setForeignKey($socialEntity->getId());
        $unified->setCreated($socialEntity->getSnippetPublishedAt());
        $unified->setText($text === null ? '' : $text);
        $unified->setImageUrl($socialEntity->getSnippetThumbnailsHighUrl());
        $unified->setVideoUrl('http://youtu.be/'.$socialEntity->getSnippetResourceIdVideoId());
    }

    /**
     * converts a instagram to a unified entity
     *
     * @param  FacebookEntityInterface     $socialEntity the instagram object
     * @param  UnifiedSocialEntityInterface $unified      the unified object
     * @return void
     */
    public function mapFacebookEntity(FacebookEntityInterface $socialEntity, UnifiedSocialEntityInterface $unified)
    {
        $text = '';
        $link = $video = $photo = null;

        switch ($socialEntity->getType()) {
            case 'status':
                $text = $socialEntity->getMessage();
                $story = $socialEntity->getStory();
                $text = $text === null ? ($story === null ? '' : $story) : $text;
                $photo = $socialEntity->getPicture();
                break;

            case 'photo':
                $photo = $socialEntity->getPicture();
                break;

            case 'link':
                $link = $socialEntity->getLink();
                $photo = $socialEntity->getPicture();
                break;

            case 'video':
                $video = $socialEntity->getSource();
                $photo = $socialEntity->getPicture();
                break;
        }

        $unified->setForeignKey($socialEntity->getId());
        $unified->setCreated($socialEntity->getCreatedTime());
        $unified->setText($text);
        $unified->setImageUrl($photo);
        $unified->setVideoUrl($video);
        $unified->setLinkUrl($link);
        $id = $socialEntity->getFromId();
        if (!empty($id)) {
            $unified->setProfileImageUrl("https://graph.facebook.com/" . $id . "/picture");
        }
    }

    /**
     * Returns an object of the type stored in the type column of the unified
     * entity. You can use this method to convert unified entites to original
     * entities.
     *
     * @param  UnifiedSocialEntityInterface $unified the unified entity that is converted
     * @return mixed
     */
    public function getOriginalEntity(UnifiedSocialEntityInterface $unified)
    {
        $repository = $this->em->getRepository($unified->getType());
        return $repository->findOneById($unified->getForeignKey());
    }
}
