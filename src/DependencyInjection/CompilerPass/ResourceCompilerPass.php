<?php

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use LAG\AdminBundle\Resource\Resource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ResourceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $resourceCollection = $container->getDefinition('lag.admin.resource_collection');
        $admins = $container->getParameter('lag.admins');

        foreach ($admins as $name => $admin) {
            $serviceId = 'lag.admin.resource.'.$name;
            $definition = new Definition(Resource::class, [
                $name,
                $admin,
            ]);
            $container->setDefinition($serviceId, $definition);

            $resourceCollection->addMethodCall('add', [
                new Reference($serviceId),
            ]);
        }
    }
}
