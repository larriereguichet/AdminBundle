<?php

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use LAG\AdminBundle\Resource\AdminResource;
use LAG\AdminBundle\Resource\ResourceCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ResourceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $resourceCollection = $container->getDefinition(ResourceCollection::class);
        $admins = $container->getParameter('lag.admins');

        foreach ($admins as $name => $admin) {
            $serviceId = 'lag.admin.resource.'.$name;
            $definition = new Definition(AdminResource::class, [
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
