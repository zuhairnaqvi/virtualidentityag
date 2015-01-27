<?php
/*
 * This file is part of the Virtual-Identity Twitter package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\TwitterBundle\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;

use VirtualIdentity\TwitterBundle\Interfaces\TwitterEntityInterface;

class TweetChangedEvent extends Event
{
    /**
     * The tweet that changed
     *
     * @var TwitterEntityInterface
     */
    protected $tweet;

    public function __construct(TwitterEntityInterface $tweet)
    {
        $this->tweet = $tweet;
    }

    /**
     * Returns the changed tweet
     *
     * @return TwitterEntityInterface
     */
    public function getTweet()
    {
        return $this->tweet;
    }
}