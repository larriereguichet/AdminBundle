<?php

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use LAG\AdminBundle\Slug\Generator\CompositeSlugGenerator;
use LAG\AdminBundle\Slug\Mapping\SlugMapping;
use LAG\AdminBundle\Slug\Mapping\SlugMappingInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SlugMappingCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(CompositeSlugGenerator::class)) {
            return;
        }
        $generators = [];

        foreach ($container->findTaggedServiceIds('lag_admin.slug_generator') as $id => $tags) {
            foreach ($tags as $tag) {
                $generators[$tag['generator']] = new Reference($id);
            }
        }
        $definition = $container->getDefinition(CompositeSlugGenerator::class);
        $definition->setLazy(true);
        $definition->setArgument('$generators', $generators);
    }
}
