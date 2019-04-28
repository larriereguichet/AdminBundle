<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\DataProviderFactory;
use LAG\AdminBundle\Factory\FormFactory;
use LAG\AdminBundle\Factory\FormFactoryInterface;
use LAG\AdminBundle\Field\Definition\FieldDefinition;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Fixtures\FakeEntity;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class FormFactoryTest extends AdminTestBase
{
    public function testService()
    {
        $this->assertServiceExists(FormFactory::class);
        $this->assertServiceExists(FormFactoryInterface::class);
    }

    public function testCreateDeleteForm()
    {
        list($factory, , $formFactory) = $this->createFactory();

        $action = $this->createActionWithConfigurationMock([
            ['form', 'form_type'],
        ]);
        $entity = new FakeEntity();
        $form = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with('form_type', $entity)
            ->willReturn($form)
        ;

        $deleteForm = $factory->createDeleteForm($action, $entity);

        $this->assertEquals($form, $deleteForm);
    }

    public function testCreateEntityForm()
    {
        list($factory, $dataProviderFactory, $formFactory) = $this->createFactory();

        $admin = $this->createAdminWithConfigurationMock([
            ['data_provider', 'my_provider'],
            ['form', null],
        ], 2, 2);
        $dataProvider = $this->createMock(DataProviderInterface::class);
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $form = $this->createMock(FormInterface::class);
        $entity = new FakeEntity();

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
        $dataProvider
            ->expects($this->once())
            ->method('getFields')
            ->willReturn([
                'createdAt' => new FieldDefinition('datetime'),
                'updatedAt' => new FieldDefinition('datetime'),
                'pandas' => new FieldDefinition('choice'),
                'array' => new FieldDefinition('array'),
                'simple_array' => new FieldDefinition('simple_array'),
            ])
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
        $formBuilder
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturn($formBuilder)
        ;
        $formBuilder
            ->expects($this->exactly(2))
            ->method('addModelTransformer')
            ->willReturnCallback(function (CallbackTransformer $transformer) {
                // TODO test transformers
//                if ('array' === $field) {
//                    $this->assertEquals('key: value'.PHP_EOL, $transformer->transform(['key' => 'value']));
//                    $this->assertEquals('tt', $transformer->reverseTransform('key: value'));
//                }
            })
        ;
        $formBuilder
            ->expects($this->exactly(3))
            ->method('add')
            ->willReturnCallback(function ($field) {
                $this->assertContains($field, [
                    'pandas',
                    'array',
                    'simple_array',
                ]);
            })
        ;

        $factory->createEntityForm($admin);
    }

    public function testCreateEntityFormWithDefinedForm()
    {
        list($factory, $dataProviderFactory, $formFactory) = $this->createFactory();

        $admin = $this->createAdminWithConfigurationMock([
            ['data_provider', 'my_provider'],
            ['form', 'my_form'],
        ], 2, 2);
        $dataProvider = $this->createMock(DataProviderInterface::class);
        $form = $this->createMock(FormInterface::class);
        $entity = new FakeEntity();

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

        $factory->createEntityForm($admin);
    }

    /**
     * @expectedException Exception
     */
    public function testCreateEntityFormWithInvalidFieldDefinition()
    {
        list($factory, $dataProviderFactory,) = $this->createFactory();

        $admin = $this->createAdminWithConfigurationMock([
            ['data_provider', 'my_provider'],
            ['form', null],
        ], 2, 2);
        $dataProvider = $this->createMock(DataProviderInterface::class);
        $entity = new FakeEntity();

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
        $dataProvider
            ->expects($this->once())
            ->method('getFields')
            ->willReturn([
                'createdAt' => null,
            ])
        ;

        $factory->createEntityForm($admin);
    }

    /**
     * @return FormFactory[]|MockObject[]
     */
    private function createFactory(): array
    {
        $dataProviderFactory = $this->createMock(DataProviderFactory::class);
        $formFactory = $this->createMock(\Symfony\Component\Form\FormFactoryInterface::class);
        $factory = new FormFactory($dataProviderFactory, $formFactory);

        return [
            $factory,
            $dataProviderFactory,
            $formFactory,
        ];
    }
}
