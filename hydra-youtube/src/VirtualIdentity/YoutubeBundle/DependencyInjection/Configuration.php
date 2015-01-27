<?php
/*
 * This file is part of the Virtual-Identity Youtube package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\YoutubeBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('virtual_identity_youtube');

        $rootNode
            ->children()
                ->arrayNode('api_requests')
                    ->defaultValue(array('youtube/v3/playlistItems?part=snippet&playlistId=PLbpi6ZahtOH6-54XcuR5kGoy4wq7QtdDF'))
                    ->info('The Youtube-Api-Requests that will be used to retrieve the youtubes')
                    ->example(array('youtube/v3/playlistItems?part=snippet&playlistId=PLbpi6ZahtOH6-54XcuR5kGoy4wq7QtdDF'))
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('social_entity_class')
                    ->defaultValue('VirtualIdentity\YoutubeBundle\Entity\YoutubeEntity')
                    ->info('The class that will be used by the YoutubeService and persisted. If overwritten, class must implement VirtualIdentity\YoutubeBundle\Interfaces\YoutubeEntityInterface')
                    ->example('MyNamespace\MyBundle\Entity\MyYoutubeEntity')
                ->end()
                ->booleanNode('auto_approve')
                    ->defaultValue(true)
                    ->info('Defines if newly synchronized youtubes will be automatically approved or not.')
                    ->example('true')
                ->end()
                ->scalarNode('host')
                    ->defaultValue('www.googleapis.com')
                    ->info('The host name used to connect to the youtube api')
                    ->example('www.googleapis.com')
                ->end()
                ->scalarNode('consumer_key')
                    ->defaultValue('')
                    ->info('Youtubes client id for authentication via OAuth')
                    ->example('CAaClamflmcaSO3124fNOSI')
                ->end()
                ->scalarNode('consumer_secret')
                    ->defaultValue('')
                    ->info('Youtubes client secret for authentication via OAuth')
                    ->example('1r10nflaKFANf30023nfnlwnlsfFLSnfFS3w02fnldkgGSDlg')
                ->end()
                ->scalarNode('token')
                    ->defaultValue('')
                    ->info('Youtubes OAuth token for authorization via OAuth1A')
                    ->example('ya29.AHES6ZRjbYVuOJjJd3SrE6OmfF0LufzReNJHJxMCtRgMrnXoYanD')
                ->end()
                ->scalarNode('refresh_token')
                    ->defaultValue('')
                    ->info('Youtubes token for refreshing the access token')
                    ->example('203587205230-FLSiesflsie323lFLK32lfKLS9205w5KFLSF')
                ->end()
                ->scalarNode('expire_date')
                    ->defaultValue(time()+3600)
                    ->info('Unix timestamp when the token should be refreshed')
                    ->example('203587205230')
                ->end()
            ->end();


        return $treeBuilder;
    }
}
