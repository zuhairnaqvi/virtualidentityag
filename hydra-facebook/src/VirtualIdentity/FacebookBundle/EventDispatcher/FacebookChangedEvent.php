<?php
/*
 * This file is part of the Virtual-Identity Facebook package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\FacebookBundle\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;

use VirtualIdentity\FacebookBundle\Interfaces\FacebookEntityInterface;

class FacebookChangedEvent extends Event
{
    /**
     * The facebook that changed
     *
     * @var FacebookEntityInterface
     */
    protected $facebook;

    public function __construct(FacebookEntityInterface $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * Returns the changed facebook
     *
     * @return FacebookEntityInterface
     */
    public function getFacebook()
    {
        return $this->facebook;
    }
}