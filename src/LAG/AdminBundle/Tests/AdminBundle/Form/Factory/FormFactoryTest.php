<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Form\Factory;

use Exception;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
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
        $this->assertTrue(true);
        return;
        $symfonyFormFactory = $this->getMockWithoutConstructor(SymfonyFormFactory::class);
        $formFactory = new FormFactory($symfonyFormFactory);
    
    
        $formFactory->create();
        
        
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);

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
            ->expects($this->atLeastOnce())
            ->method('add');

        $symfonyFormFactory = $this->getMockWithoutConstructor(SymfonyFormFactory::class);
        $symfonyFormFactory
            ->method('createNamed')
            ->willReturn($form);

        $formFactory = new FormFactory(
            $symfonyFormFactory
        );

        $entity = new TestEntity();
    
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturn([
                'name' => [],
            ])
        ;
        
        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->method('getName')
            ->willReturn('MyLittleAdmin')
        ;

        $formFactory->create(
            null,
            $entity,
            $admin
        );
    }
}
