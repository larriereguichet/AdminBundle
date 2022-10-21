<?php

declare(strict_types=1);

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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');

        if ($container->getParameter('kernel.environment') === 'dev') {
            $loader->load('services_dev.yaml');
        }
        $container->setParameter('lag_admin.application.configuration', $config);
        $container->setParameter('lag_admin.resource_paths', $config['resource_paths']);
        $container->setParameter('lag_admin.translation_domain', $config['translation_domain']);
        $container->setParameter('lag_admin.title', $config['title']);
        $container->setParameter('lag_admin.date_format', $config['date_format']);
        $container->setParameter('lag_admin.time_format', $config['time_format']);
        $container->setParameter('lag_admin.date_localization', $config['date_localization']);
    }

    public function getAlias(): string
    {
        return 'lag_admin';
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $resolvingBag = $container->getParameterBag();
        $configs = $resolvingBag->resolveValue($configs);
        $config = $this->processConfiguration(new Configuration(), $configs);

//        // TODO remove this and pass when calling template instead
//        $container->prependExtensionConfig('knp_menu', [
//            'twig' => [
//                'template' => '@LAGAdmin/menu/menu-base.html.twig',
//            ],
//        ]);

        $container->prependExtensionConfig('twig', [
            'form_themes' => ['@LAGAdmin/form/theme.html.twig'],
            'globals' => [
                'lag_admin_translation_catalog' => $config['translation_domain'],
            ],
        ]);
    }
}
