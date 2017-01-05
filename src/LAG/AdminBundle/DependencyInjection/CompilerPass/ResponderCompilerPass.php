<?php

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ResponderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('lag.admin.responder.storage')) {
            return;
        }
        $storage = $container->getDefinition('lag.admin.responder.storage');
        $responderIds = $container->findTaggedServiceIds('responder');
    
        foreach ($responderIds as $responderId) {
            $storage->addMethodCall('add', [
                $responderId,
                new Reference($responderId),
            ]);
        }
    }
}
