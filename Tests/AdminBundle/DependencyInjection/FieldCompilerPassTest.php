<?php

namespace LAG\AdminBundle\Tests\AdminBundle\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\DataProviderCompilerPass;
use LAG\AdminBundle\DependencyInjection\FieldCompilerPass;
use LAG\AdminBundle\Field\Field;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FieldCompilerPassTest extends AdminTestBase
{
    /**
     * Process method should add the definition of the tagged services to the data providers factory.
     */
    public function testProcess()
    {
        $compilerPass = new FieldCompilerPass();
        $containerBuilder = new ContainerBuilder();

        // create a tagged service definition
        $taggedServiceDefinition = new Definition();
        $taggedServiceDefinition->addTag('lag.field', [
            'type' => 'string'
        ]);

        // create the data providers factory definition
        $factoryDefinition = new Definition();

        // add them to the container builder
        $containerBuilder->addDefinitions([
            'one_field' => $taggedServiceDefinition,
            'lag.admin.field_factory' => $factoryDefinition
        ]);

        // process the compiler pass
        $compilerPass->process($containerBuilder);

        $calls = $containerBuilder->getDefinition('lag.admin.field_factory')->getMethodCalls();
        
        $this->assertEquals('addFieldMapping', $calls[0][0]);
        $this->assertEquals('string', $calls[0][1][0]);
        $this->assertEquals('one_field', $calls[0][1][1]);

        $fieldCalls = $taggedServiceDefinition->getMethodCalls();
        $this->assertCount(1, $fieldCalls);
        $this->assertEquals('setConfiguration', $fieldCalls[0][0]);

        $this->assertInstanceOf(Reference::class, $fieldCalls[0][1][0]);
        $this->assertEquals('lag.admin.application', (string)$fieldCalls[0][1][0]);

    }

    public function testProcessWrongConfiguration()
    {
        $compilerPass = new FieldCompilerPass();
        $containerBuilder = new ContainerBuilder();

        // create a tagged service definition
        $taggedServiceDefinition = new Definition();
        $taggedServiceDefinition->addTag('lag.field');

        // create the data providers factory definition
        $factoryDefinition = new Definition();

        // add them to the container builder
        $containerBuilder->addDefinitions([
            'my_custom_provider' => $taggedServiceDefinition,
            'lag.admin.field_factory' => $factoryDefinition
        ]);

        $this->assertExceptionRaised(
            InvalidConfigurationException::class,
            function () use ($compilerPass, $containerBuilder) {
                // process the compiler pass
                $compilerPass->process($containerBuilder);
            }
        );
    }

    /**
     * Process method should not change the container builder if the admin factory definition does not exists.
     */
    public function testProcessWithoutAdminFactory()
    {
        $containerBuilder = new ContainerBuilder();
        $compilerPass = new FieldCompilerPass();
        $compilerPass->process($containerBuilder);
        
        $this->assertFalse($containerBuilder->has('lag.admin.data_providers_factory'));
    }

    /**
     * Process method should not change the container builder if no tagged services were found.
     */
    public function testProcessWithoutTaggedServices()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setDefinition('lag.admin.field_factory', new Definition());

        $compilerPass = new FieldCompilerPass();
        $compilerPass->process($containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('lag.admin.field_factory'));
    }
}
