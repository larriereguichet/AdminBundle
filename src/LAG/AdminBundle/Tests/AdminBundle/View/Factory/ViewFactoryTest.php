<?php

namespace LAG\AdminBundle\Tests\AdminBundle\View\Factory;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Factory\ConfigurationFactory;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Field\Factory\FieldFactory;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\View\Factory\ViewFactory;

class ViewFactoryTest extends AdminTestBase
{
    public function testCreate()
    {
        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);

        $fields = [
            $this->getMockWithoutConstructor(FieldInterface::class),
        ];

        $fieldFactory = $this->getMockWithoutConstructor(FieldFactory::class);
        $fieldFactory
            ->expects($this->once())
            ->method('getFields')
            ->with($actionConfiguration)
            ->willReturn($fields)
        ;

        $factory = new ViewFactory($configurationFactory, $fieldFactory);

        $view = $factory->create('list', 'myAdmin', $adminConfiguration, $actionConfiguration);

        $this->assertEquals('list', $view->getActionName());
        $this->assertEquals('myAdmin', $view->getName());
        $this->assertEquals($actionConfiguration, $view->getConfiguration());
        $this->assertEquals($adminConfiguration, $view->getAdminConfiguration());
        $this->assertEquals($fields, $view->getFields());
    }
}
