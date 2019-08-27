<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Factory\FieldFactory;
use LAG\AdminBundle\Factory\ViewFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class ViewFactoryTest extends AdminTestBase
{
    public function testCreate()
    {
        list($factory, $fieldFactory,, $storage) = $this->createFactory();

        $request = new Request();

        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $entities = [];

        $storage
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($this->createApplicationConfigurationMock([
                ['base_template', 'my_template.html.twig']
            ]))
        ;

        $fieldFactory
            ->expects($this->once())
            ->method('createFields')
            ->with($actionConfiguration)
            ->willReturn([])
        ;

        $form = $this->createMock(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('createView')
        ;
        $forms = [
            'planet_form' => $form,
        ];

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

    public function testCreateAjax()
    {
        list($factory, $fieldFactory,, $storage) = $this->createFactory();

        $request = new Request([], [], [], [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $entities = [];

        $storage
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($this->createApplicationConfigurationMock([
                ['ajax_template', 'my_template.html.twig']
            ]))
        ;

        $fieldFactory
            ->expects($this->once())
            ->method('createFields')
            ->with($actionConfiguration)
            ->willReturn([])
        ;

        $form = $this->createMock(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('createView')
        ;
        $forms = [
            'planet_form' => $form,
        ];

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

    public function testCreateWithRedirection()
    {
        $request = new Request([
            'submit_and_redirect' => 'submit_and_redirect',
        ]);
        $entities = [];

        list($factory, $fieldFactory, $router,) = $this->createFactory();

        $adminConfiguration = $this->createMock(AdminConfiguration::class);
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
        $actionConfiguration = $this->createMock(ActionConfiguration::class);

        $entityForm = $this->createMock(FormInterface::class);
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

        $fieldFactory
            ->expects($this->never())
            ->method('createFields')
        ;

        $router
            ->expects($this->once())
            ->method('generate')
            ->with('planet.list')
            ->willReturn('/planet/atomize')
        ;

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

    /**
     * @return MockObject[]|ViewFactory[]
     */
    private function createFactory(): array
    {
        $fieldFactory = $this->createMock(FieldFactory::class);
        $router = $this->createMock(RouterInterface::class);
        $storage = $this->createMock(ApplicationConfigurationStorage::class);

        $factory = new ViewFactory(
            $fieldFactory,
            $router,
            $storage
        );

        return [
            $factory,
            $fieldFactory,
            $router,
            $storage
        ];
    }
}
