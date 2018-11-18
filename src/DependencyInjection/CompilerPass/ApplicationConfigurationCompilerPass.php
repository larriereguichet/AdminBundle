<?php

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApplicationConfigurationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(ApplicationConfigurationStorage::class);
        $configuration = $container->getParameter('lag.admin.application_configuration');

        $definition->addMethodCall('setConfiguration', [
            $configuration,
        ]);
    }
}
