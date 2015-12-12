<?php

namespace LAG\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LAGAdminExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (!array_key_exists('application', $config)) {
            throw new InvalidConfigurationException('Your section "application" is not found for AdminBundle configuration');
        }
        if (!array_key_exists('admins', $config)) {
            throw new InvalidConfigurationException('Your section "admins" is not found for AdminBundle configuration');
        }
        if (!array_key_exists('enable_extra_configuration', $config['application'])) {
            $config['application']['enable_extra_configuration'] = true;
        }
        $container->setParameter('lag.admins', $config['admins']);
        $container->setParameter('lag.menus', $config['menus']);
        $container->setParameter('lag.admin.application_configuration', $config['application']);
        $container->setParameter('lag.enable_extra_configuration', $config['application']['enable_extra_configuration']);


    }

    public function getAlias()
    {
        return 'lag_admin';
    }
}
