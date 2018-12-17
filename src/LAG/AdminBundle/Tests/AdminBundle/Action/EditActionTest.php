<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Controller\EditAction;
use LAG\AdminBundle\Action\Responder\EditResponder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Entity\TestSimpleEntity;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class EditActionTest extends AdminTestBase
{
    public function testInvoke()
    {
        $entity = new TestSimpleEntity();
        $request = new Request([], [], []);

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->method('getParameter')
            ->willReturnMap([
                ['form', FormType::class],
                ['form_options', []],
            ])
        ;

        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getUniqueEntity')
            ->willReturn($entity)
        ;

        $form = $this->getMockWithoutConstructor(FormInterface::class);
        $form
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $form
            ->method('isValid')
            ->willReturn(true)
        ;

        $formFactory = $this->getMockWithoutConstructor(FormFactoryInterface::class);
        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(FormType::class, $entity, [])
            ->willReturn($form)
        ;

        $responder = $this->getMockWithoutConstructor(EditResponder::class);
        $responder
            ->method('respond')
            ->with($actionConfiguration, $admin, $form, null)
        ;

        $action = new EditAction(
            'edit',
            $formFactory,
            $responder
        );
        $action->setConfiguration($actionConfiguration);
        $action->setAdmin($admin);

        $action->__invoke($request);
    }
}
