<?php
/*
 * This file is part of the Virtual-Identity Instagram package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\InstagramBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('virtual_identity_instagram');

        $rootNode
            ->children()
                ->arrayNode('api_requests')
                    ->defaultValue(array('v1/users/self/feed'))
                    ->info('The Instagram-Api-Requests that will be used to retrieve the instagrams')
                    ->example(array('v1/users/self/feed'))
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('social_entity_class')
                    ->defaultValue('VirtualIdentity\InstagramBundle\Entity\InstagramEntity')
                    ->info('The class that will be used by the InstagramService and persisted. If overwritten, class must implement VirtualIdentity\InstagramBundle\Interfaces\InstagramEntityInterface')
                    ->example('MyNamespace\MyBundle\Entity\MyInstagramEntity')
                ->end()
                ->booleanNode('auto_approve')
                    ->defaultValue(true)
                    ->info('Defines if newly synchronized instagrams will be automatically approved or not.')
                    ->example('true')
                ->end()
                ->scalarNode('host')
                    ->defaultValue('api.instagram.com')
                    ->info('The host name used to connect to the instagram api')
                    ->example('api.instagram.com')
                ->end()
                ->scalarNode('consumer_key')
                    ->defaultValue('')
                    ->info('Instagrams consumer_key for authentication via OAuth1A')
                    ->example('CAaClamflmcaSO3124fNOSI')
                ->end()
                ->scalarNode('consumer_secret')
                    ->defaultValue('')
                    ->info('Instagrams consumer_secret for authentication via OAuth1A')
                    ->example('1r10nflaKFANf30023nfnlwnlsfFLSnfFS3w02fnldkgGSDlg')
                ->end()
                ->scalarNode('token')
                    ->defaultValue('')
                    ->info('Instagrams OAuth token for authorization via OAuth1A')
                    ->example('203587205230-FLSiesflsie323lFLK32lfKLS9205w5KFLSF')
                ->end()
            ->end();


        return $treeBuilder;
    }
}
