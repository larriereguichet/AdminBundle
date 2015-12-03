<?php

namespace LAG\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ManagerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('lag.admin.factory')) {
            return;
        }

        $definition = $container->findDefinition('lag.admin.factory');
        $taggedServices = $container->findTaggedServiceIds('lag.manager');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addCustomManager',
                [$id, new Reference($id)]
            );
        }
    }
}
