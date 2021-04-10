<?php

namespace LAG\AdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class LAGAdminExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        if ('dev' === $container->getParameter('kernel.environment')) {
            $loader->load('services_dev.yaml');
        }

        if (!\array_key_exists('application', $config)) {
            $config['resources_path'] = '%kernel.project_dir%/config/admin/resources';
        }
        if (!\array_key_exists('menus', $config)) {
            $config['menus'] = [];
        }
        $container->setParameter('lag_admin.application.configuration', $config);
        $container->setParameter('lag_admin.menu.menus', $config['menus']);
        $container->setParameter('lag_admin.resources.path', $config['resources_path']);
        $container->setParameter('lag_admin.media.bundle_enabled', \array_key_exists('JKMediaBundle', $container->getParameter('kernel.bundles')));
        $container->setParameter('lag_admin.fields.mapping', $config['fields_mapping']);
    }

    public function getAlias(): string
    {
        return 'lag_admin';
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('knp_menu', [
            'twig' => [
                'template' => '@LAGAdmin/menu/menu-base.html.twig',
            ],
        ]);
    }
}
