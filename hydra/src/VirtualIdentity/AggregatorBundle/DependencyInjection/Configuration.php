<?php
/*
 * This file is part of the Virtual-Identity Social Media Aggregator package.
 *
 * (c) Virtual-Identity <development@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\AggregatorBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('virtual_identity_aggregator');

        $rootNode
            ->children()
                ->scalarNode('unified_social_entity_class')
                    ->defaultValue('VirtualIdentity\AggregatorBundle\Entity\UnifiedSocialEntity')
                    ->info('All SocialMedia entities will be unified by the AggregatorConverterService and persisted using this entity. If overwritten, class must implement VirtualIdentity\AggregatorBundle\Interfaces\UnifiedSocialEntityInterface')
                    ->example('MyNamespace\MyBundle\Entity\MyUnifiedSocialEntity')
                ->end()
                ->booleanNode('auto_approve')
                    ->defaultValue(true)
                    ->info('Defines if newly synchronized posts/tweets/etc will be automatically approved or not.')
                    ->example('true')
                ->end()
                ->arrayNode('harvested_services')
                    ->info('Array of social media services that are harvested for new items. Those services must implement the getFeed and syncDatabase method.')
                    ->example(array('@virtual_identity_twitter', '@virtual_identity_facebook', '@virtual_identity_instagram', '@virtual_identity_youtube'))
                    ->defaultValue(array('@virtual_identity_twitter'))
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
