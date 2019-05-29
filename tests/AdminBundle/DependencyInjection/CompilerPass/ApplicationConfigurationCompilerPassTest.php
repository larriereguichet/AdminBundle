<?php

namespace LAG\AdminBundle\Tests\DependencyInjection\CompilerPass;

use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\DependencyInjection\CompilerPass\ApplicationConfigurationCompilerPass;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApplicationConfigurationCompilerPassTest extends AdminTestBase
{
    public function testProcess(): void
    {
        $configuration = [
            'my_little_value' => 'Oh Yeah !',
        ];

        $storage = $this->createContainerDefinition(ApplicationConfigurationStorage::class);
        $container = new ContainerBuilder();
        $container->setDefinition(ApplicationConfigurationStorage::class, $storage);
        $container->setParameter('lag.admin.application_configuration', $configuration);

        $compilerPass = new ApplicationConfigurationCompilerPass();
        $compilerPass->process($container);

        $calls = $storage->getMethodCalls();

        $this->assertCount(1, $calls);
        $this->assertEquals('setConfiguration', $calls[0][0]);
        $this->assertEquals($configuration, $calls[0][1][0]);
    }
}
