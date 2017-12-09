<?php

namespace LAG\AdminBundle\Tests\DependencyInjection\CompilerPass;

use LAG\AdminBundle\DependencyInjection\CompilerPass\ResourceCompilerPass;
use LAG\AdminBundle\Resource\Resource;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ResourceCompilerPassTest extends AdminTestBase
{
    public function testProcess()
    {
        $resourceCollection = $this->getMockWithoutConstructor(Definition::class);
        $resourceCollection
            ->expects($this->once())
            ->method('addMethodCall')
            ->willReturnCallback(function ($name, $parameters) {
                $this->assertEquals('add', $name);
                $this->assertCount(1, $parameters);
                $this->assertInstanceOf(Resource::class, $parameters[0]);

                /** @var Resource $resource */
                $resource = $parameters[0];
                $this->assertEquals('panda', $resource->getName());
            })
        ;

        $builder = $this->getMockWithoutConstructor(ContainerBuilder::class);
        $builder
            ->expects($this->once())
            ->method('getDefinition')
            ->with('lag.admin.resource_collection')
            ->willReturn($resourceCollection)
        ;
        $builder
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->willReturnMap([
                ['lag.admins', [
                    'panda' => [
                        'entity' => 'TestEntity',
                    ],
                ]],
                ['lag.admin.application_configuration', [

                ]],
            ])
        ;

        $compilerPass = new ResourceCompilerPass();
        $compilerPass->process($builder);
    }

    public function testProcessWithApplicationConfigurationException()
    {
        $resourceCollection = $this->getMockWithoutConstructor(Definition::class);

        $builder = $this->getMockWithoutConstructor(ContainerBuilder::class);
        $builder
            ->expects($this->once())
            ->method('getDefinition')
            ->with('lag.admin.resource_collection')
            ->willReturn($resourceCollection)
        ;
        $builder
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->willReturnMap([
                ['lag.admins', [
                    'panda' => [
                        'entity' => 'TestEntity',
                    ],
                ]],
                ['lag.admin.application_configuration', [
                    'enable_extra_configuration' => 42,
                ]],
            ])
        ;

        $compilerPass = new ResourceCompilerPass();

        $this->assertExceptionRaised(\Exception::class, function () use ($compilerPass, $builder) {
            $compilerPass->process($builder);
        });
    }

    public function testProcessWithAdminConfigurationException()
    {
        $resourceCollection = $this->getMockWithoutConstructor(Definition::class);

        $builder = $this->getMockWithoutConstructor(ContainerBuilder::class);
        $builder
            ->expects($this->once())
            ->method('getDefinition')
            ->with('lag.admin.resource_collection')
            ->willReturn($resourceCollection)
        ;
        $builder
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->willReturnMap([
                ['lag.admins', [
                    'panda' => [],
                ]],
                ['lag.admin.application_configuration', []],
            ])
        ;

        $compilerPass = new ResourceCompilerPass();

        $this->assertExceptionRaised(\Exception::class, function () use ($compilerPass, $builder) {
            $compilerPass->process($builder);
        });
    }
}
