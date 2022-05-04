<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Bridge\Doctrine\ORMDataProvider;
use LAG\AdminBundle\DataProvider\Registry\DataProviderRegistryInterface;
use LAG\AdminBundle\Factory\FieldFactoryInterface;
use LAG\AdminBundle\Field\Definition\FieldDefinitionInterface;
use LAG\AdminBundle\Form\Factory\FormFactory;
use LAG\AdminBundle\Form\Factory\FormFactoryInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminFormFactoryTest extends TestCase
{
    private MockObject $dataProviderRegistry;
    private MockObject $formFactory;
    private FormFactoryInterface $adminFormFactory;
    private MockObject $fieldFactory;

    public function testServiceExists(): void
    {
        $this->assertServiceExists(FormFactory::class);
        $this->assertServiceExists(FormFactoryInterface::class);
    }

    public function testCreateEntityForm(): void
    {
        $admin = $this->createMock(AdminInterface::class);
        $request = new Request();
        $data = new stdClass();
        $action = $this->createMock(ActionInterface::class);

        $adminConfiguration = new AdminConfiguration();
        $adminConfiguration->configure([
            'name' => 'my_admin',
            'entity' => 'MyClass',
        ]);
        $actionConfiguration = new ActionConfiguration();
        $actionConfiguration->configure([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'fields' => [],
            'path' => '/my-action',
            'route' => 'my_action',
            'form' => 'MyForm',
            'template' => 'my-template.html.twig',
        ]);

        $dataProvider = $this->createMock(ORMDataProvider::class);

        $admin
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
        $admin
            ->expects($this->once())
            ->method('getEntityClass')
            ->willReturn('MyEntity')
        ;
        $this
            ->dataProviderRegistry
            ->expects($this->once())
            ->method('get')
            ->with('doctrine')
            ->willReturn($dataProvider)
        ;

        $dataProvider
            ->expects($this->once())
            ->method('create')
            ->willReturn($data)
        ;

        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $form = $this->createMock(FormInterface::class);
        $this
            ->formFactory
            ->expects($this->once())
            ->method('create')
            ->with('MyForm')
            ->willReturn($form)
        ;

        $this->adminFormFactory->createEntityForm($admin, $request);
    }

    public function testCreateDeleteForm(): void
    {
        $admin = $this->createMock(AdminInterface::class);
        $action = $this->createMock(ActionInterface::class);

        $actionConfiguration = new ActionConfiguration();
        $actionConfiguration->configure([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'path' => '/my-action',
            'route' => 'my_action_route',
            'template' => 'my_action.html.twig',
            'fields' => [],
            'form' => 'MyFormType',
        ]);
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;
        $request = new Request();
        $data = new stdClass();
        $data->test = true;

        $form = $this->createMock(FormInterface::class);

        $this
            ->formFactory
            ->expects($this->once())
            ->method('create')
            ->with('MyFormType', $data, [])
            ->willReturn($form)
        ;

        $this->adminFormFactory->createDeleteForm($admin, $request, $data);
    }

    public function testCreateEntityFormWithoutForm(): void
    {
        $admin = $this->createMock(AdminInterface::class);
        $request = new Request();
        $data = new stdClass();
        $action = $this->createMock(ActionInterface::class);

        $adminConfiguration = new AdminConfiguration();
        $adminConfiguration->configure([
            'name' => 'my_admin',
            'entity' => 'MyClass',
        ]);

        $actionConfiguration = new ActionConfiguration();
        $actionConfiguration->configure([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'form' => null,
            'form_options' => ['label' => 'my_label'],
            'path' => '/my-action',
            'route' => 'my_action_route',
            'template' => 'my_action.html.twig',
            'fields' => [],
        ]);

        $dataProvider = $this->createMock(ORMDataProvider::class);

        $admin
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
        $admin
            ->expects($this->once())
            ->method('getEntityClass')
            ->willReturn('MyEntity')
        ;
        $this
            ->dataProviderRegistry
            ->expects($this->once())
            ->method('get')
            ->with('doctrine')
            ->willReturn($dataProvider)
        ;

        $dataProvider
            ->expects($this->once())
            ->method('create')
            ->willReturn($data)
        ;

        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $this
            ->formFactory
            ->expects($this->once())
            ->method('createBuilder')
            ->with(FormType::class, $data, ['label' => false])
            ->willReturn($formBuilder)
        ;

        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);
        $idFieldDefinition = $this->createMock(FieldDefinitionInterface::class);
        $dateTimeFieldDefinition = $this->createMock(FieldDefinitionInterface::class);
//        $dateTimeFieldDefinition
//            ->expects($this->atLeastOnce())
//            ->method('getType')
//            ->willReturn('datetime')
//        ;
//
//        $fieldDefinition
//            ->expects($this->atLeastOnce())
//            ->method('getType')
//            ->willReturn('choice')
//        ;

        $form = $this->createMock(FormInterface::class);
        $formBuilder
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($form)
        ;

        $this->adminFormFactory->createEntityForm($admin, $request);
    }

    public function setUp(): void
    {
        $this->dataProviderRegistry = $this->createMock(DataProviderRegistryInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->fieldFactory = $this->createMock(FieldFactoryInterface::class);

        $this->adminFormFactory = new FormFactory($this->dataProviderRegistry, $this->formFactory, $this->fieldFactory);
    }
}
