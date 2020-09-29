<?php

namespace LAG\AdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Load AdminBundle configuration into the container.
 */
class LAGAdminExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Load the configuration into the container.
     */
    public function load(array $configs, ContainerBuilder $builder)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($builder, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        if ('dev' === $builder->getParameter('kernel.environment')) {
            $loader->load('services_dev.yaml');
        }
        $adminsConfig = [];
        $enableExtraConfig = true;

        if (!key_exists('application', $config)) {
            $config['application'] = [
                'enable_menus' => true,
                'resources_path' => '%kernel.project_dir%/config/admin/resources',
            ];
        }

        if (key_exists('enable_extra_configuration', $config['application'])) {
            $enableExtraConfig = $config['application']['enable_extra_configuration'];
        }
        $applicationConfig = $config['application'];

        if (key_exists('admins', $config)) {
            $adminsConfig = $config['admins'];
        }

        if (!key_exists('menus', $config)) {
            $config['menus'] = [];
        }
        $builder->setParameter('lag.admin.enable_extra_configuration', $enableExtraConfig);
        $builder->setParameter('lag.admin.application_configuration', $applicationConfig);
        $builder->setParameter('lag.admins', $adminsConfig);
        $builder->setParameter('lag.menus', $config['menus']);
        $builder->setParameter('lag.enable_menus', $config['application']['enable_menus']);
        $builder->setParameter('lag.admin.resources_path', $config['application']['resources_path']);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'lag_admin';
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('knp_menu', [
            'twig' => [
                'template' => '@LAGAdmin/Menu/menu-base.html.twig',
            ],
        ]);
    }
}
