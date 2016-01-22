<?php

namespace LAG\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('lag.admin.factory')) {
            return;
        }

        $definition = $container->findDefinition('lag.admin.factory');
        $taggedServices = $container->findTaggedServiceIds('data_provider');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addDataProvider',
                [
                    $id,
                    new Reference($id),
                ]
            );
        }
    }
}
