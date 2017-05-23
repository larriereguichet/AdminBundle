<?php

namespace LAG\AdminBundle\DependencyInjection;

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
    
        $applicationConfig = [];
        $adminsConfig = [];
        $menusConfig = [];
        $enableExtraConfig = true;
        $translationPattern = null;
        
        if (array_key_exists('application', $config)) {
            if (array_key_exists('enable_extra_configuration', $config['application'])) {
                $enableExtraConfig = $config['application']['enable_extra_configuration'];
            }
            if (array_key_exists('translation', $config['application']) &&
                array_key_exists('pattern', $config['application']['translation'])) {
                $translationPattern = $config['application']['translation']['pattern'];
            }
            $applicationConfig = $config['application'];
        }
        if (array_key_exists('admin', $config)) {
            $adminsConfig = $config['admin'];
        }
        if (array_key_exists('menu', $config)) {
            $menusConfig = $config['menu'];
        }
        $builder->setParameter('lag.admin.enable_extra_configuration', $enableExtraConfig);
        $builder->setParameter('lag.admin.application_configuration', $applicationConfig);
        $builder->setParameter('lag.admins', $adminsConfig);
        $builder->setParameter('lag.menus', $menusConfig);
        $builder->setParameter('lag.translation_pattern', $translationPattern);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'lag_admin';
    }
}
