<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\DeleteAction;
use LAG\AdminBundle\Action\Responder\DeleteResponder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Entity\TestSimpleEntity;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class DeleteActionTest extends AdminTestBase
{
    public function testInvoke()
    {
        $entity = new TestSimpleEntity();
        $request = new Request([], [], []);
        
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->method('getParameter')
            ->willReturnMap([
                ['form', 'MyForm'],
                ['form_options', [
                    'key' => 'value',
                ]],
            ])
        ;
    
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('remove')
            ->willReturn($entity)
        ;
        
        $form = $this->getMockWithoutConstructor(FormInterface::class);
        $form
            ->method('isValid')
            ->willReturn(true)
        ;
        $form
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        
        $formFactory = $this->getMockWithoutConstructor(FormFactoryInterface::class);
        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with('MyForm', null, [
                'key' => 'value',
            ])
            ->willReturn($form)
        ;

        $responder = $this->getMockWithoutConstructor(DeleteResponder::class);
        $responder
            ->method('respond')
            ->with($actionConfiguration, $admin, $form)
        ;
        $action = new DeleteAction(
            'delete',
            $formFactory,
            $responder
        );
        $action->setConfiguration($actionConfiguration);
        $action->setAdmin($admin);
        
        $action->__invoke($request);
    }
}
