<?php

namespace LAG\AdminBundle\Tests\AdminBundle\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\DataProviderCompilerPass;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DataProviderCompilerPassTest extends AdminTestBase
{
    /**
     * Process method should add the definition of the tagged services to the data providers factory.
     */
    public function testProcess()
    {
        // create a tagged service definition
        $taggedServiceDefinition = new Definition();
        $taggedServiceDefinition->addTag('data_provider');

        // create the data providers factory definition
        $factoryDefinition = new Definition();

        // add them to the container builder
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            'my_custom_provider' => $taggedServiceDefinition,
            'lag.admin.data_providers_factory' => $factoryDefinition
        ]);

        // process the compiler pass
        $compilerPass = new DataProviderCompilerPass();
        $compilerPass->process($containerBuilder);

        $calls = $containerBuilder
            ->getDefinition('lag.admin.data_providers_factory')
            ->getMethodCalls();

        $this->assertEquals('addDataProvider', $calls[0][0]);
        $this->assertEquals('my_custom_provider', $calls[0][1][0]);
        $this->assertInstanceOf(Reference::class, $calls[0][1][1]);
    }

    /**
     * Process method should not change the container builder if the admin factory definition does not exists.
     */
    public function testProcessWithoutAdminFactory()
    {
        $containerBuilder = new ContainerBuilder();
        $compilerPass = new DataProviderCompilerPass();
        $compilerPass->process($containerBuilder);
        
        $this->assertFalse($containerBuilder->has('lag.admin.data_providers_factory'));
    }

    /**
     * Process method should not change the container builder if no tagged services were found.
     */
    public function testProcessWithoutTaggedServices()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setDefinition('lag.admin.factory', new Definition());

        $compilerPass = new DataProviderCompilerPass();
        $compilerPass->process($containerBuilder);
        
        $this->assertFalse($containerBuilder->has('lag.admin.data_providers_factory'));
    }
}
