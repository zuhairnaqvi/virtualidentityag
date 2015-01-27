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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VirtualIdentityTwitterExtension extends Extension
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
            'virtual_identity_twitter.auto_approve',
            $config['auto_approve']
        );

        $container->setParameter(
            'virtual_identity_twitter.host',
            $config['host']
        );

        $container->setParameter(
            'virtual_identity_twitter.consumer_key',
            $config['consumer_key']
        );

        $container->setParameter(
            'virtual_identity_twitter.consumer_secret',
            $config['consumer_secret']
        );

        $container->setParameter(
            'virtual_identity_twitter.token',
            $config['token']
        );

        $container->setParameter(
            'virtual_identity_twitter.secret',
            $config['secret']
        );

    }
}
