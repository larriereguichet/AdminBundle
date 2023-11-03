<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection;

use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Vich\UploaderBundle\Naming\SmartUniqueNamer;

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
            if ($container->hasDefinition(AdminDataCollector::class)) {
                $definition = $container->getDefinition(AdminDataCollector::class);
                $definition
                    ->setArgument('$applicationConfiguration', $config)
                    ->clearTag('lag_admin.request_context_provider')
                ;
            }
        }
        $container->setParameter('lag_admin.application_name', $config['default_application']);
        $container->setParameter('lag_admin.application_parameter', $config['request']['application_parameter']);
        $container->setParameter('lag_admin.resource_parameter', $config['request']['resource_parameter']);
        $container->setParameter('lag_admin.operation_parameter', $config['request']['operation_parameter']);
        $container->setParameter('lag_admin.translation_domain', $config['translation_domain']);

        $container->setParameter('lag_admin.application.configuration', $config);
        $container->setParameter('lag_admin.resource_paths', $config['resource_paths']);
        $container->setParameter('lag_admin.translation_domain', $config['translation_domain']);
        $container->setParameter('lag_admin.title', $config['title']);
        $container->setParameter('lag_admin.date_format', $config['date_format']);
        $container->setParameter('lag_admin.time_format', $config['time_format']);
        $container->setParameter('lag_admin.date_localization', $config['date_localization']);
        $container->setParameter('lag_admin.filter_events', $config['filter_events']);
        $container->setParameter('lag_admin.grids', $config['grids']);

        $container->registerForAutoconfiguration(ContextProviderInterface::class)
            ->addTag('lag_admin.request_context_provider')
        ;
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
        ]);

        $container->prependExtensionConfig('flysystem', [
            'storages' => [
                'lag_admin_image.storage' => [
                    'adapter' => 'local',
                    'options' => [
                        'directory' => $container->getParameter('kernel.project_dir').'/public/admin/images',
                    ],
                ],
            ],
        ]);

        $container->prependExtensionConfig('vich_uploader', [
            'db_driver' => 'orm',
            'storage' => 'flysystem',
            'metadata' => ['type' => 'attribute'],
            'mappings' => [
                'lag_admin_images' => [
                    'uri_prefix' => '/admin/images',
                    'upload_destination' => 'lag_admin_image.storage',
                    'namer' => SmartUniqueNamer::class,
                    'inject_on_load' => false,
                    'delete_on_update' => true,
                    'delete_on_remove' => true,
                ],
            ],
        ]);
    }
}
