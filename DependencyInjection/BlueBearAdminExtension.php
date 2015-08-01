<?php

namespace BlueBear\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BlueBearAdminExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if (!array_key_exists('application', $config)) {
            throw new InvalidConfigurationException('Your section "application" is not found for AdminBundle configuration');
        }
        if (!array_key_exists('admins', $config)) {
            throw new InvalidConfigurationException('Your section "admins" is not found for AdminBundle configuration');
        }
        $container->setParameter('bluebear.admins', $config['admins']);
        $container->setParameter('bluebear.menus', $config['menus']);
        $container->setParameter('bluebear.admin.application', $config['application']);
    }

    public function getAlias()
    {
        return 'bluebear_admin';
    }

}
