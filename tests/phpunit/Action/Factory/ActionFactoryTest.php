<?php

namespace LAG\AdminBundle\Tests\Action\Factory;

use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Factory\ActionConfigurationFactoryInterface;
use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Action\Factory\ActionFactoryInterface;
use LAG\AdminBundle\Admin\Resource\AdminResource;
use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\ActionEvent;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ActionFactoryTest extends TestCase
{
    private ActionFactory $actionFactory;

    private MockObject $eventDispatcher;

    private MockObject $configurationFactory;

    private MockObject $resourceRegistry;

    public function testServiceExists(): void
    {
        $this->assertServiceExists(ActionFactory::class);
        $this->assertServiceExists(ActionFactoryInterface::class);
    }

    public function testCreate(): void
    {
        $adminResource = $this->createMock(AdminResource::class);
        $actionConfiguration = $this->createMock(ActionConfiguration::class);

        $this
            ->resourceRegistry
            ->expects($this->once())
            ->method('get')
            ->with('my_admin')
            ->willReturn($adminResource)
        ;

        $adminResource
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([
                'actions' => [
                    'list' => ['title' => 'My title'],
                ],
            ])
        ;

        $this
            ->configurationFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                'my_admin', 'list', [
                'title' => 'My title',
                'admin_name' => 'my_admin',
                'action_class' => Action::class,
            ])
            ->willReturn($actionConfiguration)
        ;

        $actionConfiguration
            ->expects($this->once())
            ->method('getActionClass')
            ->willReturn(Action::class)
        ;

        $this
            ->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn(function ($actionEvent, $eventName) {
                $this->assertInstanceOf(ActionEvent::class, $actionEvent);
                $this->assertEquals(AdminEvents::ACTION_CREATE, $eventName);
            })
        ;

        $action = $this->actionFactory->create('list', [
            'admin_name' => 'my_admin',
            'action_class' => Action::class,
        ]);
        $this->assertEquals($actionConfiguration, $action->getConfiguration());
        $this->assertEquals('list', $action->getName());
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->configurationFactory = $this->createMock(ActionConfigurationFactoryInterface::class);
        $this->resourceRegistry = $this->createMock(ResourceRegistryInterface::class);
        $this->actionFactory = new ActionFactory($this->eventDispatcher, $this->configurationFactory, $this->resourceRegistry);
    }
}
