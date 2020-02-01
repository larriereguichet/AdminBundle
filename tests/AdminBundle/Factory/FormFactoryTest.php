<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Factory\DataProviderFactory;
use LAG\AdminBundle\Factory\FormFactory;
use LAG\AdminBundle\Factory\FormFactoryInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Fixtures\FakeEntity;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormFactoryTest extends AdminTestBase
{
    public function testService()
    {
        $this->assertServiceExists(FormFactory::class);
        $this->assertServiceExists(FormFactoryInterface::class);
    }

    public function testCreateDeleteForm()
    {
        list($factory,, $formFactory) = $this->createFactory();

        $action = $this->createActionWithConfigurationMock([
            ['form', 'form_type'],
        ]);
        $entity = new FakeEntity();
        $request = new Request();
        $form = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with('form_type', $entity)
            ->willReturn($form)
        ;

        $deleteForm = $factory->createDeleteForm($action, $request, $entity);

        $this->assertEquals($form, $deleteForm);
    }

    public function testCreateEntityForm()
    {
        list($factory, $dataProviderFactory, $formFactory) = $this->createFactory();

        $admin = $this->createAdminWithConfigurationMock([
            ['data_provider', 'my_provider'],
            ['form', null],
        ]);
        $dataProvider = $this->createMock(DataProviderInterface::class);
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $form = $this->createMock(FormInterface::class);
        $entity = new FakeEntity();
        $request = new Request();

        $dataProviderFactory
            ->expects($this->once())
            ->method('get')
            ->with('my_provider')
            ->willReturn($dataProvider)
        ;
        $dataProvider
            ->expects($this->once())
            ->method('create')
            ->willReturn($entity)
        ;
        $formFactory
            ->expects($this->once())
            ->method('createBuilder')
            ->with(FormType::class, $entity, [
                'label' => false,
            ])
            ->willReturn($formBuilder)
        ;
        $formBuilder
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($form)
        ;
        $factory->createEntityForm($admin, $request);
    }

    public function testCreateEntityFormWithDefinedForm()
    {
        list($factory, $dataProviderFactory, $formFactory) = $this->createFactory();

        $admin = $this->createAdminWithConfigurationMock([
            ['data_provider', 'my_provider'],
            ['form', 'my_form'],
        ]);
        $dataProvider = $this->createMock(DataProviderInterface::class);
        $form = $this->createMock(FormInterface::class);
        $entity = new FakeEntity();
        $request = new Request();

        $dataProviderFactory
            ->expects($this->once())
            ->method('get')
            ->with('my_provider')
            ->willReturn($dataProvider)
        ;
        $dataProvider
            ->expects($this->once())
            ->method('create')
            ->willReturn($entity)
        ;
        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with('my_form')
            ->willReturn($form)
        ;

        $factory->createEntityForm($admin, $request);
    }

    /**
     * @return FormFactory[]|MockObject[]
     */
    private function createFactory(): array
    {
        $dataProviderFactory = $this->createMock(DataProviderFactory::class);
        $formFactory = $this->createMock(\Symfony\Component\Form\FormFactoryInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $factory = new FormFactory($dataProviderFactory, $formFactory, $eventDispatcher);

        return [
            $factory,
            $dataProviderFactory,
            $formFactory,
            $eventDispatcher,
        ];
    }
}
