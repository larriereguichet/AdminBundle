<?php

declare(strict_types=1);

namespace LAG\AdminBundle;

use LAG\AdminBundle\DependencyInjection\CompilerPass\MediaStorageCompilerPass;
use LAG\AdminBundle\DependencyInjection\CompilerPass\ObjectMapperCompilerPass;
use LAG\AdminBundle\DependencyInjection\CompilerPass\WorkflowCompilerPass;
use LAG\AdminBundle\DependencyInjection\LAGAdminExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class LAGAdminBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new WorkflowCompilerPass());
        $container->addCompilerPass(new MediaStorageCompilerPass());
        $container->addCompilerPass(new ObjectMapperCompilerPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ExtensionInterface
    {
        return new LAGAdminExtension();
    }
}
