<?php

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use LAG\AdminBundle\Configuration\AdminConfiguration;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApplicationConfigurationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(AdminConfiguration::class);
        $configuration = $container->getParameter('lag.admin.application_configuration');

        $definition->addMethodCall('setConfiguration', [
            $configuration,
        ]);
    }
}
