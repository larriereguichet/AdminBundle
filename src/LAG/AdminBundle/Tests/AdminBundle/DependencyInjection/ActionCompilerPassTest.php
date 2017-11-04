<?php

namespace LAG\AdminBundle\Tests\AdminBundle\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\CompilerPass\ActionCompilerPass;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Fixtures\Action\TestAction;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ActionCompilerPassTest extends AdminTestBase
{
    public function testProcess()
    {
        $actionRegistry = new Definition();
        $testAction = new Definition(TestAction::class);
        $testAction->setTags([
            LAGAdminBundle::SERVICE_TAG_ACTION => LAGAdminBundle::SERVICE_TAG_ACTION,
        ]);
        
        $builder = new ContainerBuilder();
        $builder->setDefinition(LAGAdminBundle::SERVICE_ID_ACTION_REGISTRY, $actionRegistry);
        $builder->setDefinition('test_action', $testAction);
        
        $compilerPass = new ActionCompilerPass();
        $compilerPass->process($builder);
    
        $this->assertCount(5, $actionRegistry->getMethodCalls());
    }
    
    public function testProcessWithoutActionFactory()
    {
        $builder = new ContainerBuilder();
        
        $compilerPass = new ActionCompilerPass();
        $compilerPass->process($builder);
    
        // everything went fine
        $this->assertTrue(true);
    }
}
