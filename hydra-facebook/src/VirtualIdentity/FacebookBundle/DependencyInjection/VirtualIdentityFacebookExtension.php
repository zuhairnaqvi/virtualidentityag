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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VirtualIdentityFacebookExtension extends Extension
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
            'virtual_identity_facebook.api_requests',
            $config['api_requests']
        );

        $container->setParameter(
            'virtual_identity_facebook.social_entity_class',
            $config['social_entity_class']
        );

        $container->setParameter(
            'virtual_identity_facebook.auto_approve',
            $config['auto_approve']
        );

        $container->setParameter(
            'virtual_identity_facebook.host',
            $config['host']
        );

        $container->setParameter(
            'virtual_identity_facebook.app_id',
            $config['app_id']
        );

        $container->setParameter(
            'virtual_identity_facebook.app_secret',
            $config['app_secret']
        );

        $container->setParameter(
            'virtual_identity_facebook.permissions',
            $config['permissions']
        );

        $container->setParameter(
            'virtual_identity_facebook.token',
            $config['token']
        );
    }
}
