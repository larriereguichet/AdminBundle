<?php

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use LAG\AdminBundle\LAGAdminBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RepositoryCompilerPass implements CompilerPassInterface
{
    /**
     * Add the tagged repositories to the factory.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('lag.admin.repository_factory')) {
            return;
        }
        $definition = $container->findDefinition('lag.admin.repository_factory');
        $taggedServices = $container->findTaggedServiceIds(LAGAdminBundle::SERVICE_TAG_REPOSITORY);

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
