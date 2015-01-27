<?php
/*
 * This file is part of the Virtual-Identity Twitter package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\TwitterBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('virtual_identity_twitter');

        $rootNode
            ->children()
                ->booleanNode('auto_approve')
                    ->defaultValue(true)
                    ->info('Defines if newly synchronized tweets will be automatically approved or not.')
                    ->example('true')
                ->end()
                ->scalarNode('host')
                    ->defaultValue('api.twitter.com')
                    ->info('The host name used to connect to the twitter api')
                    ->example('api.twitter.com')
                ->end()
                ->scalarNode('consumer_key')
                    ->defaultValue('')
                    ->info('Twitters consumer_key for authentication via OAuth1A')
                    ->example('CAaClamflmcaSO3124fNOSI')
                ->end()
                ->scalarNode('consumer_secret')
                    ->defaultValue('')
                    ->info('Twitters consumer_secret for authentication via OAuth1A')
                    ->example('1r10nflaKFANf30023nfnlwnlsfFLSnfFS3w02fnldkgGSDlg')
                ->end()
                ->scalarNode('token')
                    ->defaultValue('')
                    ->info('Twitters OAuth token for authorization via OAuth1A')
                    ->example('203587205230-FLSiesflsie323lFLK32lfKLS9205w5KFLSF')
                ->end()
                ->scalarNode('secret')
                    ->defaultValue('')
                    ->info('Twitters OAuth token secret for authorization via OAuth1A')
                    ->example('ENH4q32fLKDlfnls33lfnl348slfALfk')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
