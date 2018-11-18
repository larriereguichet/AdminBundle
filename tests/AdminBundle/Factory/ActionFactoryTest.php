<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\ActionFactory;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Fixtures\ActionFixture;
use LAG\AdminBundle\Tests\Fixtures\EntityFixture;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ActionFactoryTest extends AdminTestBase
{
    public function testCreate()
    {
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['actions', [
                    'list' => [],
                ]],
                ['entity', 'MyLittleTauntaun'],
                ['class', EntityFixture::class],
            ])
        ;

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->once())
            ->method('getParameter')
            ->willReturnMap([
                ['class', ActionFixture::class],
            ])
        ;

        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $configurationFactory
            ->expects($this->once())
            ->method('createActionConfiguration')
            ->with('list',  [], 'tauntaun', $adminConfiguration)
            ->willReturn($actionConfiguration)
        ;

        $factory = new ActionFactory(
            $eventDispatcher,
            $configurationFactory
        );

        $factory->create('list', 'tauntaun', $adminConfiguration);
    }

    public function testCreateWithMissingAction()
    {
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);

        $factory = new ActionFactory(
            $eventDispatcher,
            $configurationFactory
        );

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['actions', []],
                ['entity', 'MyLittleTauntaun'],
            ])
        ;

        $this->assertExceptionRaised(Exception::class, function () use ($factory, $adminConfiguration) {
            $factory->create('list', 'tauntaun', $adminConfiguration);
        });
    }

    public function testCreateWithWrongActionClass()
    {
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['actions', [
                    'list' => [],
                ]],
                ['entity', 'MyLittleTauntaun'],
                ['class', EntityFixture::class],
            ])
        ;

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->once())
            ->method('getParameter')
            ->willReturnMap([
                ['class', EntityFixture::class],
            ])
        ;

        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $configurationFactory
            ->expects($this->once())
            ->method('createActionConfiguration')
            ->with('list',  [], 'tauntaun', $adminConfiguration)
            ->willReturn($actionConfiguration)
        ;

        $factory = new ActionFactory(
            $eventDispatcher,
            $configurationFactory
        );

        $this->assertExceptionRaised(Exception::class, function () use ($factory, $adminConfiguration) {
            $factory->create('list', 'tauntaun', $adminConfiguration);
        });
    }
}
