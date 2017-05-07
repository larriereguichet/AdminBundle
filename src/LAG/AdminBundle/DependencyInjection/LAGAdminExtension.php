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
     * @param ContainerBuilder $builder
     */
    public function load(array $configs, ContainerBuilder $builder)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($builder, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (!array_key_exists('application', $config)) {
            throw new InvalidConfigurationException(
                'Your section "application" is not found for AdminBundle configuration'
            );
        }
        if (!array_key_exists('enable_extra_configuration', $config['application'])) {
            $config['application']['enable_extra_configuration'] = true;
        }
        $builder->setParameter('lag.admins', $config['admins']);
        $builder->setParameter('lag.menus', $config['menus']);
        $builder->setParameter('lag.admin.application_configuration', $config['application']);
        $builder->setParameter('lag.enable_extra_configuration', $config['application']['enable_extra_configuration']);
        
        if (!array_key_exists('pattern', $config['application']['translation'])) {
            $config['application']['translation']['pattern'] = null;
        }
        $builder->setParameter('lag.translation_pattern', $config['application']['translation']['pattern']);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'lag_admin';
    }
}
