<?php

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApplicationConfigurationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('lag.admin.configuration');
        $configuration = $container->getParameter('lag.admin.application_configuration');

        $definition->addMethodCall('setConfiguration', [
            $configuration,
        ]);
    }
}
