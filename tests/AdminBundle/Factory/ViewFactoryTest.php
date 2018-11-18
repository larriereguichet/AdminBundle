<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Factory\FieldFactory;
use LAG\AdminBundle\Factory\MenuFactory;
use LAG\AdminBundle\Factory\ViewFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class ViewFactoryTest extends AdminTestBase
{
    public function testCreate()
    {
        $request = new Request();
        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $entities = [];

        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);

        $fieldFactory = $this->getMockWithoutConstructor(FieldFactory::class);
        $fieldFactory
            ->expects($this->once())
            ->method('getFields')
            ->with($actionConfiguration)
            ->willReturn([])
        ;

        $menuFactory = $this->getMockWithoutConstructor(MenuFactory::class);
        $router = $this->getMockWithoutConstructor(RouterInterface::class);

        $factory = new ViewFactory(
            $configurationFactory,
            $fieldFactory,
            $menuFactory,
            $router
        );

        $form = $this->getMockWithoutConstructor(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('createView')
        ;
        $forms = [
            'planet_form' => $form,
        ];

        $view = $factory->create(
            $request,
            'atomize',
            'planet',
            $adminConfiguration,
            $actionConfiguration,
            $entities,
            $forms
        );
    }

    public function testCreateWithRedirection()
    {
        $request = new Request([
            'submit_and_redirect' => 'submit_and_redirect',
        ]);
        $entities = [];

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['actions', [
                    'list' => [],
                ]],
                ['routing_name_pattern', '{admin}.{action}'],
            ])
        ;
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);

        $entityForm = $this->getMockWithoutConstructor(FormInterface::class);
        $entityForm
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $entityForm
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;
        $forms = [
            'entity' => $entityForm,
        ];

        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);

        $fieldFactory = $this->getMockWithoutConstructor(FieldFactory::class);
        $fieldFactory
            ->expects($this->never())
            ->method('getFields')
        ;

        $menuFactory = $this->getMockWithoutConstructor(MenuFactory::class);
        $router = $this->getMockWithoutConstructor(RouterInterface::class);
        $router
            ->expects($this->once())
            ->method('generate')
            ->with('planet.list')
            ->willReturn('/planet/atomize')
        ;

        $factory = new ViewFactory(
            $configurationFactory,
            $fieldFactory,
            $menuFactory,
            $router
        );

        $factory->create(
            $request,
            'atomize',
            'planet',
            $adminConfiguration,
            $actionConfiguration,
            $entities,
            $forms
        );
    }
}
