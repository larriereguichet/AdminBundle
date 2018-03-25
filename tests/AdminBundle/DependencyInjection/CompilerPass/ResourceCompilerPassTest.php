<?php

namespace LAG\AdminBundle\Tests\DependencyInjection\CompilerPass;

use LAG\AdminBundle\DependencyInjection\CompilerPass\ResourceCompilerPass;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

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
                $this->assertInstanceOf(Reference::class, $parameters[0]);
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
            ->expects($this->exactly(1))
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
}
