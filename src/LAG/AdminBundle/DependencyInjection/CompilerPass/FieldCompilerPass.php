<?php

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FieldCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('lag.admin.field_factory')) {
            return;
        }
        // get field factory definition
        $definition = $container->findDefinition(
            'lag.admin.field_factory'
        );
        // find field tagged services
        $taggedServices = $container->findTaggedServiceIds(
            'lag.field'
        );
        // foreach tagged, with add this field type to the field factory
        foreach ($taggedServices as $id => $tags) {
            if (empty($tags[0]['type'])) {
                throw new InvalidConfigurationException('You should defined a "type" attribute for field tag for service '.$id);
            }
            // add allowed field type to the field factory
            $definition->addMethodCall('addFieldMapping', [
                $tags[0]['type'], $id,
            ]);
            // add application configuration for field
            $tagDefinition = $container->findDefinition($id);
            $tagDefinition->addMethodCall('setConfiguration', [
                new Reference('lag.admin.application'),
            ]);
        }
    }
}
