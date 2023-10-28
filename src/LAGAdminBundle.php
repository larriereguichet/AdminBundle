<?php

declare(strict_types=1);

namespace LAG\AdminBundle;

use LAG\AdminBundle\DependencyInjection\CompilerPass\EventCompilerPass;
use LAG\AdminBundle\DependencyInjection\CompilerPass\SlugMappingCompilerPass;
use LAG\AdminBundle\DependencyInjection\LAGAdminExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class LAGAdminBundle extends AbstractBundle
{
    // Request Admin parameters
    // TODO from configuration
    public const REQUEST_PARAMETER_ADMIN = '_admin';
    public const REQUEST_PARAMETER_ACTION = '_action';

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new EventCompilerPass());
        $container->addCompilerPass(new SlugMappingCompilerPass());
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
