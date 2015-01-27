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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VirtualIdentityYoutubeExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter(
            'virtual_identity_youtube.api_requests',
            $config['api_requests']
        );

        $container->setParameter(
            'virtual_identity_youtube.social_entity_class',
            $config['social_entity_class']
        );

        $container->setParameter(
            'virtual_identity_youtube.auto_approve',
            $config['auto_approve']
        );

        $container->setParameter(
            'virtual_identity_youtube.host',
            $config['host']
        );

        $container->setParameter(
            'virtual_identity_youtube.consumer_key',
            $config['consumer_key']
        );

        $container->setParameter(
            'virtual_identity_youtube.consumer_secret',
            $config['consumer_secret']
        );

        $container->setParameter(
            'virtual_identity_youtube.token',
            $config['token']
        );

        $container->setParameter(
            'virtual_identity_youtube.refresh_token',
            $config['refresh_token']
        );

        $container->setParameter(
            'virtual_identity_youtube.expire_date',
            $config['expire_date']
        );
    }
}
