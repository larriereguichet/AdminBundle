<?php

declare(strict_types=1);

namespace LAG\AdminBundle;

use LAG\AdminBundle\DependencyInjection\CompilerPass\EventCompilerPass;
use LAG\AdminBundle\DependencyInjection\CompilerPass\WorkflowCompilerPass;
use LAG\AdminBundle\DependencyInjection\LAGAdminExtension;
use LAG\AdminBundle\Grid\Builder\GridBuilderInterface;
use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Resource\Locator\MetadataLocatorInterface;
use LAG\AdminBundle\Resource\Locator\ResourceLocatorInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class LAGAdminBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new EventCompilerPass());
        $container->addCompilerPass(new WorkflowCompilerPass());

        $container->registerForAutoconfiguration(ProviderInterface::class)->addTag('lag_admin.state_provider');
        $container->registerForAutoconfiguration(ProcessorInterface::class)->addTag('lag_admin.state_processor');
        $container->registerForAutoconfiguration(ContextProviderInterface::class)->addTag('lag_admin.request_context_provider');
        $container->registerForAutoconfiguration(MetadataLocatorInterface::class)->addTag('lag_admin.metadata_locator');
        $container->registerForAutoconfiguration(ResourceLocatorInterface::class)->addTag('lag_admin.resource_locator');
        $container->registerForAutoconfiguration(GridBuilderInterface::class)->addTag('lag_admin.grid_builder');
        $container->registerForAutoconfiguration(DataTransformerInterface::class)->addTag('lag_admin.data_transformer');
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new LAGAdminExtension();
    }
}
