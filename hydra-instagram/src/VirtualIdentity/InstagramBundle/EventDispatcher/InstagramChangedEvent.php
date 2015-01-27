<?php
/*
 * This file is part of the Virtual-Identity Instagram package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\InstagramBundle\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;

use VirtualIdentity\InstagramBundle\Interfaces\InstagramEntityInterface;

class InstagramChangedEvent extends Event
{
    /**
     * The instagram that changed
     *
     * @var InstagramEntityInterface
     */
    protected $instagram;

    public function __construct(InstagramEntityInterface $instagram)
    {
        $this->instagram = $instagram;
    }

    /**
     * Returns the changed instagram
     *
     * @return InstagramEntityInterface
     */
    public function getInstagram()
    {
        return $this->instagram;
    }
}