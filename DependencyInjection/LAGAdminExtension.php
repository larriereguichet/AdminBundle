<?php

namespace LAG\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Load AdminBundle configuration into the container.
 */
class LAGAdminExtension extends Extension
{
    /**
     * Load the configuration into the container.
     *
     * @param array $configs
     * @param ContainerBuilder $container
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
        if (!array_key_exists('enable_extra_configuration', $config['application'])) {
            $config['application']['enable_extra_configuration'] = true;
        }
        $container->setParameter('lag.admins', $config['admins']);
        $container->setParameter('lag.menus', $config['menus']);
        $container->setParameter('lag.admin.application_configuration', $config['application']);
        $container->setParameter('lag.enable_extra_configuration', $config['application']['enable_extra_configuration']);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'lag_admin';
    }
}
