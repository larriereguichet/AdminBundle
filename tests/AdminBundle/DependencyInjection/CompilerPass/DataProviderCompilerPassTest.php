<?php

namespace LAG\AdminBundle\Tests\DependencyInjection\CompilerPass;

use LAG\AdminBundle\DependencyInjection\CompilerPass\DataProviderCompilerPass;
use LAG\AdminBundle\Factory\DataProviderFactory;
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
        $definition = $this->createMock(Definition::class);
        $definition
            ->expects($this->once())
            ->method('addMethodCall')
            ->with('add', [
                'data_provider',
                new Reference('data_provider'),
            ])
        ;
        $builder = $this->createMock(ContainerBuilder::class);
        $builder
            ->expects($this->once())
            ->method('findDefinition')
            ->with(DataProviderFactory::class)
            ->willReturn($definition)
        ;
        $builder
            ->expects($this->once())
            ->method('has')
            ->with(DataProviderFactory::class)
            ->willReturn(true)
        ;
        $builder
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('lag.admin.data_provider')
            ->willReturn([
                'data_provider' => [],
            ])
        ;

        $compilerPass = new DataProviderCompilerPass();
        $compilerPass->process($builder);
    }

    public function testProcessWithoutConfiguration()
    {
        $builder = $this->createMock(ContainerBuilder::class);

        $compilerPass = new DataProviderCompilerPass();
        $compilerPass->process($builder);

        $builder
            ->expects($this->never())
            ->method('findDefinition')
        ;
        $builder
            ->expects($this->never())
            ->method('findTaggedServiceIds')
        ;
    }
}
