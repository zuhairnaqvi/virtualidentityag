<?php
/*
 * This file is part of the Virtual-Identity Youtube package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\YoutubeBundle\Interfaces;

interface YoutubeEntityInterface
{
    public function getId();
    public function setSnippetTitle($snippetTitle);
    public function getSnippetTitle();
    public function setSnippetDescription($snippetDescription);
    public function getSnippetDescription();
    public function setSnippetResourceIdVideoId($snippetResourceIdVideoId);
    public function getSnippetResourceIdVideoId();
    public function setSnippetThumbnailsHighUrl($snippetThumbnailsHighUrl);
    public function getSnippetThumbnailsHighUrl();
    public function setSnippetPublishedAt(\DateTime $snippetPublishedAt);
    public function getSnippetPublishedAt();
    public function setRaw($raw);
    public function getRaw();
    public function setApproved($approved);
    public function getApproved();
}
