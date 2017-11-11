<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Controller\CreateAction;
use LAG\AdminBundle\Action\Responder\CreateResponder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Entity\TestSimpleEntity;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CreateActionTest extends AdminTestBase
{
    public function testInvoke()
    {
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->method('getParameter')
            ->willReturnMap([
                ['form', 'MyForm'],
                ['form_options', [
                    'my_option' => 'my_value',
                ]],
            ])
        ;
    
        $entity = new TestSimpleEntity();
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('create')
            ->willReturn($entity)
        ;
        $request = new Request([], [], []);
        $form = $this->getMockWithoutConstructor(FormInterface::class);
        
        $formFactory = $this->getMockWithoutConstructor(FormFactoryInterface::class);
        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with('MyForm', $entity, [
                'my_option' => 'my_value',
            ])
            ->willReturn($form)
        ;
    
        $responder = $this->getMockWithoutConstructor(CreateResponder::class);
        $responder
            ->expects($this->once())
            ->method('respond')
            ->with($actionConfiguration, $admin, $form, null)
        ;
        
        $action = new CreateAction(
            'create',
            $formFactory,
            $responder
        );
        $action->setConfiguration($actionConfiguration);
        $action->setAdmin($admin);
        
        $action->__invoke($request);
    }
}
