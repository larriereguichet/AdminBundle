<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use LAG\AdminBundle\Config\Mapper\ResourceMapper;
use LAG\AdminBundle\DependencyInjection\Locator\ClassLocator;
use LAG\AdminBundle\Exception\Resource\EmptyResourceNameException;
use LAG\AdminBundle\Grid\Builder\GridBuilderInterface;
use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Resource\Factory\DefinitionFactoryInterface;
use LAG\AdminBundle\Resource\Locator\PropertyLocatorInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use League\FlysystemBundle\FlysystemBundle;
use Liip\ImagineBundle\LiipImagineBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

use function Symfony\Component\String\u;

final class LAGAdminExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->loadServices($container);

        $resources = $this->loadResources($config);
        $applications = $this->loadApplications($config, $resources);
        $grids = $this->loadGrids($config);

        $resourceFactory = $container->getDefinition(DefinitionFactoryInterface::class);
        $resourceFactory->setArguments([
            '$applications' => $applications,
            '$resources' => $resources,
            '$grids' => $grids,
            '$propertyLocator' => new Reference(PropertyLocatorInterface::class),
        ]);

        $container->setParameter('lag_admin.media_directory', $config['media_directory']);
        $container->setParameter('lag_admin.application_parameter', $config['request']['application_parameter']);
        $container->setParameter('lag_admin.resource_parameter', $config['request']['resource_parameter']);
        $container->setParameter('lag_admin.operation_parameter', $config['request']['operation_parameter']);

        $container->registerForAutoconfiguration(ProviderInterface::class)->addTag('lag_admin.state_provider');
        $container->registerForAutoconfiguration(ProcessorInterface::class)->addTag('lag_admin.state_processor');
        $container->registerForAutoconfiguration(ContextBuilderInterface::class)->addTag('lag_admin.request_context_provider');
        $container->registerForAutoconfiguration(PropertyLocatorInterface::class)->addTag('lag_admin.property_locator');
        $container->registerForAutoconfiguration(GridBuilderInterface::class)->addTag('lag_admin.grid_builder');
        $container->registerForAutoconfiguration(DataTransformerInterface::class)->addTag('lag_admin.data_transformer');
    }

    public function getAlias(): string
    {
        return 'lag_admin';
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependCacheConfiguration($container);
        $this->prependValidationConfiguration($container);
        $this->prependTwigConfiguration($container);
        $this->prependFlysystemConfiguration($container);
        $this->prependLiipImagineConfiguration($container);
        $this->prependPagerfantaConfiguration($container);
        $this->prependKnpMenuConfiguration($container);
    }

    private function loadServices(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');
        $locator = new FileLocator(__DIR__.'/../../config');

        $loader = new PhpFileLoader($container, $locator);
        $loader->load('services.php');

        if ($container->getParameter('kernel.environment') === 'dev') {
            $loader->load('services_dev.php');
        }

        if (\in_array(FlysystemBundle::class, $bundles)) {
            $loader->load('services/bridges/flysystem.php');
        }

        if (\in_array(KnpMenuBundle::class, $bundles)) {
            $loader->load('services/bridges/knp_menu.php');
        }

        if (\in_array(LiipImagineBundle::class, $bundles)) {
            $loader->load('services/bridges/liip_imagine.php');
        }

        if (\in_array(DoctrineBundle::class, $bundles)) {
            $loader->load('services/bridges/doctrine.php');
        }
        // TODO use editor js
        $loader->load('services/bridges/quill_js.php');
    }

    private function loadResources(array $config): array
    {
        $resources = [];
        $locator = new ClassLocator();
        $mapper = new ResourceMapper();

        $paths = $config['mapping']['paths'] ?? [];
        $defaultApplication = $config['default_application'] ?? 'admin';

        foreach ($config['resources'] ?? [] as $resource) {
            $resources[$resource['application'].'.'.$resource['name']] = $resource;
        }

        foreach ($locator->locateClassesByPaths($paths) as $resourceClass) {
            $reflectionClass = new \ReflectionClass($resourceClass);
            $attributes = $reflectionClass->getAttributes(Resource::class);

            foreach ($attributes as $attribute) {
                /** @var resource $resource */
                $resource = $attribute->newInstance();

                if (!$resource->getName()) {
                    $resource = $resource->withName(
                        u($reflectionClass->getShortName())
                            ->snake()
                            ->lower()
                            ->toString()
                    );
                }

                if (!$resource->getApplication()) {
                    $resource = $resource->withApplication($defaultApplication);
                }

                if (!$resource->getResourceClass()) {
                    $resource = $resource->withResourceClass($reflectionClass->getName());
                }

                if (!$resource->getName()) {
                    throw new EmptyResourceNameException($resourceClass);
                }
                $resources[$resource->getFullName()] = $mapper->toArray($resource);
            }
        }

        return $resources;
    }

    private function loadApplications(array $config, array $resources): array
    {
        $applications = $config['applications'] ?? [];

        /** @var resource $resource */
        foreach ($resources as $resource) {
            if (!empty($resource['application']) && empty($applications[$resource['application']])) {
                $applications[$resource->getApplication()] = ['name' => $resource['application']];
            }
        }

        foreach ($applications as $name => $application) {
            $application['name'] = $name;
            $applications[$name] = $application;
        }

        return $applications;
    }

    private function loadGrids(array $config): array
    {
        $grids = [];

        foreach ($config['grids'] ?? [] as $grid) {
            $grids[$grid['name']] = $grid;
        }

        return $grids;
    }

    private function prependCacheConfiguration(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('framework', [
            'cache' => [
                'pools' => ['lag_admin.cache' => null],
            ],
        ]);
    }

    private function prependValidationConfiguration(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('validation', [
            'enabled' => true,
            'auto_mapping' => ['paths' => ['@LAGAdmin/src/Metadata']],
        ]);
    }

    private function prependTwigConfiguration(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('twig', [
            'form_themes' => ['@LAGAdmin/forms/theme.html.twig'],
            'globals' => ['lag_admin' => '@lag_admin.twig.global'],
        ]);
    }

    private function prependFlysystemConfiguration(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('flysystem', [
            'storages' => [
                'lag_admin_image.storage' => [
                    'adapter' => 'local',
                    'options' => ['directory' => '%kernel.project_dir%/public/%lag_admin.media_directory%'],
                    'public_url_generator' => 'lag_admin.filesystem.public_url_generator',
                ],
            ],
        ]);
    }

    private function prependLiipImagineConfiguration(ContainerBuilder $container): void
    {
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
    }

    private function prependPagerfantaConfiguration(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('babdev_pagerfanta', [
            'default_view' => 'twig',
            'default_twig_template' => '@BabDevPagerfanta/twitter_bootstrap5.html.twig',
        ]);
    }

    private function prependKnpMenuConfiguration(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('knp_menu', [
            'twig' => [
                'template' => '@LAGAdmin/menu/menu-base.html.twig',
            ],
        ]);
    }
}
