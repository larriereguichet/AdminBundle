<?php

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use LAG\AdminBundle\LAGAdminBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ActionCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $builder
     */
    public function process(ContainerBuilder $builder)
    {
        if (!$builder->hasDefinition(LAGAdminBundle::SERVICE_ID_ACTION_REGISTRY)) {
            return;
        }
        $actionRegistry = $builder->getDefinition(LAGAdminBundle::SERVICE_ID_ACTION_REGISTRY);
        $actions = $builder->findTaggedServiceIds(LAGAdminBundle::SERVICE_TAG_ACTION);
    
        //var_dump($actions);
        
        foreach ($actions as $id => $attributes) {
            $actionRegistry
                ->addMethodCall('add', [
                    $id,
                    new Reference($id),
                ]
            );
        }
    }
}
