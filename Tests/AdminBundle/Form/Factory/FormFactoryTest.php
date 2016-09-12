<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Form\Factory;

use Exception;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Field\Field\StringField;
use LAG\AdminBundle\Form\Factory\FormFactory;
use LAG\AdminBundle\Tests\AdminBundle\Form\Type\TestFormType;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory as SymfonyFormFactory;
use Test\TestBundle\Entity\TestEntity;

class FormFactoryTest extends AdminTestBase
{
    public function testCreate()
    {
        $symfonyFormFactory = $this
            ->getMockBuilder(SymfonyFormFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $formFactory = new FormFactory(
            $symfonyFormFactory
        );
        $admin = $this
            ->getMockBuilder(AdminInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        // an exception SHOULD be raised if an invalid entity is provided
        $this->invalidEntityTest($formFactory, $admin);

        // if a form type is provided, we SHOULD call the symfony form type factory
        $this->symfonyFactoryTest($formFactory, $symfonyFormFactory, $admin);

        // if no form type is provided, we SHOULD guess the form
        $this->guessFormType();
    }

    /**
     * @param FormFactory $formFactory
     * @param AdminInterface $admin
     */
    protected function invalidEntityTest(FormFactory $formFactory, AdminInterface $admin)
    {
        $this->assertExceptionRaised(Exception::class, function () use ($formFactory, $admin) {
            $formFactory->create(
                TestFormType::class,
                [],
                $admin
            );
        });
    }

    /**
     * @param FormFactory $formFactory
     * @param PHPUnit_Framework_MockObject_MockObject $symfonyFormFactory
     * @param AdminInterface $admin
     */
    protected function symfonyFactoryTest(
        FormFactory $formFactory,
        PHPUnit_Framework_MockObject_MockObject $symfonyFormFactory,
        AdminInterface $admin
    ) {
        $symfonyFormFactory
            ->expects($this->once())
            ->method('create');
        $entity = new TestEntity();

        $formFactory->create(
            TestFormType::class,
            $entity,
            $admin
        );
    }

    protected function guessFormType()
    {
        $form = $this
            ->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->getMock();
        $form
            ->expects($this->exactly(2))
            ->method('add');

        $symfonyFormFactory = $this
            ->getMockBuilder(SymfonyFormFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $symfonyFormFactory
            ->method('createNamed')
            ->willReturn($form);

        $formFactory = new FormFactory(
            $symfonyFormFactory
        );

        $entity = new TestEntity();
        $action = $this
            ->getMockBuilder(ActionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $action
            ->method('getFields')
            ->willReturn([
                new StringField(),
                new StringField(),
            ]);

        $admin = $this
            ->getMockBuilder(AdminInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $admin
            ->method('getName')
            ->willReturn('MyLittleAdmin');
        $admin
            ->method('getCurrentAction')
            ->willReturn($action);

        $formFactory->create(
            null,
            $entity,
            $admin
        );
    }
}
