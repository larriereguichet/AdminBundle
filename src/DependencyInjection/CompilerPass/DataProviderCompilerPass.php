<?php

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataProviderCompilerPass implements CompilerPassInterface
{
    /**
     * Add the tagged data provider to the DataProviderFactory.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('lag.admin.data_provider_factory')) {
            return;
        }
        $definition = $container->findDefinition('lag.admin.data_provider_factory');
        $taggedServices = $container->findTaggedServiceIds('lag.admin.data_provider');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'add',
                [
                    $id,
                    new Reference($id),
                ]
            );
        }
    }
}
