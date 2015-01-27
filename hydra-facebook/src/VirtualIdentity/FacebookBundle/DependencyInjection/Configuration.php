<?php
/*
 * This file is part of the Virtual-Identity Facebook package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\FacebookBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('virtual_identity_facebook');

        $rootNode
            ->children()
                ->arrayNode('api_requests')
                    ->defaultValue(array('me/feed'))
                    ->info('The Facebook-Api-Requests that will be used to retrieve the facebooks')
                    ->example(array('me/feed'))
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('social_entity_class')
                    ->defaultValue('VirtualIdentity\FacebookBundle\Entity\FacebookEntity')
                    ->info('The class that will be used by the FacebookService and persisted. If overwritten, class must implement VirtualIdentity\FacebookBundle\Interfaces\FacebookEntityInterface')
                    ->example('MyNamespace\MyBundle\Entity\MyFacebookEntity')
                ->end()
                ->booleanNode('auto_approve')
                    ->defaultValue(true)
                    ->info('Defines if newly synchronized facebooks will be automatically approved or not.')
                    ->example('true')
                ->end()
                ->scalarNode('host')
                    ->defaultValue('graph.facebook.com')
                    ->info('The host name used to connect to the facebook graph')
                    ->example('graph.facebook.com')
                ->end()
                ->scalarNode('app_id')
                    ->defaultValue('')
                    ->info('Facebooks app_id')
                    ->example('CAaClamflmcaSO3124fNOSI')
                ->end()
                ->scalarNode('app_secret')
                    ->defaultValue('')
                    ->info('Facebooks app_secret')
                    ->example('1r10nflaKFANf30023nfnlwnlsfFLSnfFS3w02fnldkgGSDlg')
                ->end()
                ->scalarNode('permissions')
                    ->defaultValue('email,read_stream')
                    ->info('Which permissions to ask the user for')
                    ->example('email,read_stream')
                ->end()
                ->scalarNode('token')
                    ->defaultValue('')
                    ->info('Facebooks access token')
                    ->example('203587205230-FLSiesflsie323lFLK32lfKLS9205w5KFLSF')
                ->end()
            ->end();


        return $treeBuilder;
    }
}
