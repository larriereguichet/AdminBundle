<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Controller\ListAction;
use LAG\AdminBundle\Action\Responder\ListResponder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Filter\Factory\FilterFormBuilder;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Entity\TestSimpleEntity;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ListActionTest extends AdminTestBase
{
    public function testInvoke()
    {
        $request = new Request();
        $entity = new TestSimpleEntity();

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->method('getParameter')
            ->willReturnMap([
                ['form', 'MyForm'],
                ['form_options', [
                    'required' => false,
                ]],
            ])
        ;

        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $admin
            ->expects($this->once())
            ->method('getEntities')
            ->willReturn([
                $entity,
            ])
        ;

        $form = $this->getMockWithoutConstructor(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;

        $filterForm = $this->getMockWithoutConstructor(FormInterface::class);
        $filterForm
            ->expects($this->once())
            ->method('handleRequest')
        ;

        $formFactory = $this->getMockWithoutConstructor(FormFactoryInterface::class);
        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with('MyForm', [
                $entity,
            ], [
                'required' => false,
            ])
            ->willReturn($form)
        ;

        $responder = $this->getMockWithoutConstructor(ListResponder::class);
        $responder
            ->method('respond')
            ->with($actionConfiguration, $admin, $form, $filterForm)
        ;

        $builder = $this->getMockWithoutConstructor(FilterFormBuilder::class);
        $builder
            ->method('build')
            ->with($actionConfiguration)
            ->willReturn($filterForm)
        ;

        $action = new ListAction(
            'list',
            $formFactory,
            $responder,
            $builder
        );
        $action->setConfiguration($actionConfiguration);
        $action->setAdmin($admin);

        $action->__invoke($request);
    }
}
