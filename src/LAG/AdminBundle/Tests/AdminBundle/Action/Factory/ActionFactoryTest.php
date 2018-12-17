<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Factory;

use Exception;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Event\ActionCreatedEvent;
use LAG\AdminBundle\Action\Event\ActionEvents;
use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Factory\ConfigurationFactory;
use LAG\AdminBundle\Action\Registry\Registry;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Action\Event\BeforeConfigurationEvent;
use LAG\AdminBundle\Tests\AdminTestBase;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class ActionFactoryTest extends AdminTestBase
{
    public function testInjectConfigurationWithoutActions()
    {
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);
        $actionRegistry = $this->getMockWithoutConstructor(Registry::class);

        $actionFactory = new ActionFactory(
            $configurationFactory,
            $eventDispatcher,
            $actionRegistry
        );

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->once())
            ->method('getParameter')
            ->with('actions')
            ->willReturn([
                'list' => [
                ],
            ])
        ;

        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;

        $controller = $this->getMockWithoutConstructor(ActionInterface::class);
        $controller
            ->expects($this->exactly(2))
            ->method('getAdmin')
            ->willReturn($admin)
        ;
        $request = new Request();

        $actionFactory->injectConfiguration($controller, $request);
    }

    /**
     * If the Controller is not an ActionInterface, nothing should be done.
     */
    public function testInjectConfigurationWithoutActionInterface()
    {
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $actionRegistry = $this->getMockWithoutConstructor(Registry::class);

        // no event should be dispatched
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
        ;

        $actionFactory = new ActionFactory(
            $configurationFactory,
            $eventDispatcher,
            $actionRegistry
        );
        $controller = $this->getMockWithoutConstructor(Controller::class);
        $request = new Request();

        $actionFactory->injectConfiguration($controller, $request);
    }

    /**
     * If the Controller has no Admin, nothing should be done.
     */
    public function testInjectConfigurationWithoutAdmin()
    {
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $actionRegistry = $this->getMockWithoutConstructor(Registry::class);

        // no event should be dispatched
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
        ;

        $actionFactory = new ActionFactory(
            $configurationFactory,
            $eventDispatcher,
            $actionRegistry
        );
        $controller = $this->getMockWithoutConstructor(ActionInterface::class);
        $request = new Request();

        $actionFactory->injectConfiguration($controller, $request);
    }

    public function testInjectConfiguration()
    {
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->once())
            ->method('getParameter')
            ->with('actions')
            ->willReturn([
                'list' => [
                ],
            ])
        ;
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
        $admin
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('my_little_admin')
        ;
        $actionName = 'list';

        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $configurationFactory
            ->expects($this->once())
            ->method('create')
            ->with($actionName, 'my_little_admin', $adminConfiguration, [])
            ->willReturn($actionConfiguration)
        ;

        $controller = $this->getMockWithoutConstructor(ActionInterface::class);
        $controller
            ->expects($this->atLeastOnce())
            ->method('getAdmin')
            ->willReturn($admin)
        ;

        $event = new BeforeConfigurationEvent('list', [], $admin);
        $event2 = new ActionCreatedEvent($controller, $controller->getAdmin());

        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnMap([
                [ActionEvents::BEFORE_CONFIGURATION, $event, null],
                [ActionEvents::ACTION_CREATED, $event2, null],
            ])
        ;
        $actionRegistry = $this->getMockWithoutConstructor(Registry::class);

        $request = $this->getMockWithoutConstructor(Request::class);
        $request
            ->expects($this->once())
            ->method('get')
            ->with('_route_params')
            ->willReturn([
                '_action' => $actionName,
            ])
        ;

        $actionFactory = new ActionFactory(
            $configurationFactory,
            $eventDispatcher,
            $actionRegistry
        );

        $actionFactory->injectConfiguration($controller, $request);
    }

    public function testGetActions()
    {
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);

        $actionRegistry = $this->getMockWithoutConstructor(Registry::class);
        $actionRegistry
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['my_own_service', 'test'],
                ['lag.admin.actions.edit', 'test2'],
            ])
        ;
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);

        $actionFactory = new ActionFactory(
            $configurationFactory,
            $eventDispatcher,
            $actionRegistry
        );
        $actions = $actionFactory->getActions('test', [
            'actions' => [
                'list' => [
                    'service' => 'my_own_service',
                ],
                'edit' => [
                ],
            ],
        ]);

        $this->assertEquals([
            'list' => 'test',
            'edit' => 'test2',
        ], $actions);
    }

    public function testGetActionsWithInvalidConfiguration()
    {
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $actionRegistry = $this->getMockWithoutConstructor(Registry::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);

        $actionFactory = new ActionFactory(
            $configurationFactory,
            $eventDispatcher,
            $actionRegistry
        );

        $this->assertExceptionRaised(Exception::class, function () use ($actionFactory) {
            $actionFactory->getActions('test', [
                'lol',
            ]);
        });

        $this->assertExceptionRaised(LogicException::class, function () use ($actionFactory) {
            $actionFactory->getActions('test', [
                'actions' => [
                    'my-action' => [],
                ],
            ]);
        });
    }
}
