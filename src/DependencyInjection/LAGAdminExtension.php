<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection;

use LAG\AdminBundle\Grid\Builder\GridBuilderInterface;
use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Resource\Locator\MetadataLocatorInterface;
use LAG\AdminBundle\Resource\Locator\ResourceLocatorInterface;
use LAG\AdminBundle\Resource\Registry\ApplicationRegistryInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
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

        $locator = new FileLocator(__DIR__.'/../../config');
        $loader = new Loader\PhpFileLoader($container, $locator);
        $loader->load('services.php');

        if ($container->getParameter('kernel.environment') === 'dev') {
            $loader->load('services_dev.php');
        }

        $applicationRegistry = $container->getDefinition(ApplicationRegistryInterface::class);
        $applicationRegistry->setArgument('$configurations', $config['applications'] ?? []);

        $container->setParameter('lag_admin.resource_paths', $config['resource_paths']);
        $container->setParameter('lag_admin.grid_paths', $config['grid_paths']);

        $container->setParameter('lag_admin.application_name', $config['default_application']);
        $container->setParameter('lag_admin.application_parameter', $config['request']['application_parameter']);
        $container->setParameter('lag_admin.resource_parameter', $config['request']['resource_parameter']);
        $container->setParameter('lag_admin.operation_parameter', $config['request']['operation_parameter']);
        $container->setParameter('lag_admin.translation_domain', $config['translation_domain']);

        $container->setParameter('lag_admin.application.configuration', $config);
        $container->setParameter('lag_admin.translation_domain', $config['translation_domain']);
        $container->setParameter('lag_admin.title', $config['title']);
        $container->setParameter('lag_admin.date_format', $config['date_format']);
        $container->setParameter('lag_admin.time_format', $config['time_format']);
        $container->setParameter('lag_admin.date_localization', $config['date_localization']);
        $container->setParameter('lag_admin.filter_events', $config['filter_events']);
        $container->setParameter('lag_admin.media_directory', $config['media_directory']);
        $container->setParameter('lag_admin.upload_storage', $config['uploads']['storage']);
        $container->setParameter('lag_admin.grids', $config['grids'] ?? []); // TODO remove

        $container->registerForAutoconfiguration(ProviderInterface::class)->addTag('lag_admin.state_provider');
        $container->registerForAutoconfiguration(ProcessorInterface::class)->addTag('lag_admin.state_processor');
        $container->registerForAutoconfiguration(ContextProviderInterface::class)->addTag('lag_admin.request_context_provider');
        $container->registerForAutoconfiguration(MetadataLocatorInterface::class)->addTag('lag_admin.metadata_locator');
        $container->registerForAutoconfiguration(ResourceLocatorInterface::class)->addTag('lag_admin.resource_locator');
        $container->registerForAutoconfiguration(GridBuilderInterface::class)->addTag('lag_admin.grid_builder');
        $container->registerForAutoconfiguration(DataTransformerInterface::class)->addTag('lag_admin.grid.data_transformer');
    }

    public function getAlias(): string
    {
        return 'lag_admin';
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('validation', [
            'enabled' => true,
            'enable_annotations' => true,
            'auto_mapping' => ['paths' => ['@LAGAdmin/src/Metadata']],
        ]);

        $container->prependExtensionConfig('twig', [
            'form_themes' => ['@LAGAdmin/forms/theme.html.twig'],
            'globals' => ['lag_admin' => '@lag_admin.twig_vars']
        ]);

        $container->prependExtensionConfig('flysystem', [
            'storages' => [
                'lag_admin_image.storage' => [
                    'adapter' => 'local',
                    'options' => ['directory' => '%kernel.project_dir%/public/%lag_admin.media_directory%'],
                    'public_url_generator' => 'lag_admin.filesystem.public_url_generator',
                ],
            ],
        ]);

        $container->prependExtensionConfig('liip_imagine', [
            'twig' => ['mode' => 'lazy'],
            'filter_sets' => [
                'lag_admin_thumbnail' => [
                    'filters' => [
                        'thumbnail' => ['size' => [100, 100]],
                    ],
                ],
                'lag_admin_full' => [],
            ],
            'loaders' => [
                'lag_admin' => [
                    'flysystem' => [
                        'filesystem_service' => 'lag_admin_image.storage',
                    ],
                ],
            ],
            'data_loader' => 'lag_admin',
        ]);

        $container->prependExtensionConfig('babdev_pagerfanta', [
            'default_view' => 'twig',
            'default_twig_template' => '@BabDevPagerfanta/twitter_bootstrap5.html.twig',
        ]);

        $container->prependExtensionConfig('knp_menu', [
            'twig' => [
                'template' => '@LAGAdmin/menu/menu-base.html.twig',
            ],
        ]);
    }
}
