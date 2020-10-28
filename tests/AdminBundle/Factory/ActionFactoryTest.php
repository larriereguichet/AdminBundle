<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\ActionEvent;
use LAG\AdminBundle\Factory\ActionFactory;
use LAG\AdminBundle\Factory\ActionFactoryInterface;
use LAG\AdminBundle\Factory\Configuration\ActionConfigurationFactoryInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ActionFactoryTest extends TestCase
{
    private MockObject $eventDispatcher;
    private MockObject $configurationFactory;
    private ActionFactory $actionFactory;

    public function testServiceExists(): void
    {
        $this->assertServiceExists(ActionFactory::class);
        $this->assertServiceExists(ActionFactoryInterface::class);
    }

    public function testCreate(): void
    {
        $actionConfiguration = $this->createMock(ActionConfiguration::class);

        $this->configurationFactory
            ->expects($this->once())
            ->method('create')
            ->with('list', [])
            ->willReturn($actionConfiguration)
        ;

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn(function ($actionEvent, $eventName) {
                $this->assertInstanceOf(ActionEvent::class, $actionEvent);
                $this->assertEquals(AdminEvents::ACTION_CREATE, $eventName);
            })
        ;

        $this->actionFactory->create('list', []);
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->configurationFactory = $this->createMock(ActionConfigurationFactoryInterface::class);
        $this->actionFactory = new ActionFactory($this->eventDispatcher, $this->configurationFactory);
    }
}
