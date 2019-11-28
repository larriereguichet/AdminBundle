<?php

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use LAG\AdminBundle\Factory\DataProviderFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataProviderCompilerPass implements CompilerPassInterface
{
    /**
     * Add the tagged data provider to the DataProviderFactory.
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(DataProviderFactory::class)) {
            return;
        }
        $definition = $container->findDefinition(DataProviderFactory::class);
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
