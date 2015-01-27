<?php
/*
 * This file is part of the Virtual-Identity Youtube package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\YoutubeBundle\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;

use VirtualIdentity\YoutubeBundle\Interfaces\YoutubeEntityInterface;

class YoutubeChangedEvent extends Event
{
    /**
     * The youtube that changed
     *
     * @var YoutubeEntityInterface
     */
    protected $youtube;

    public function __construct(YoutubeEntityInterface $youtube)
    {
        $this->youtube = $youtube;
    }

    /**
     * Returns the changed youtube
     *
     * @return YoutubeEntityInterface
     */
    public function getYoutube()
    {
        return $this->youtube;
    }
}