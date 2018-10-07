<?php

namespace LAG\AdminBundle\Tests\Event\Subscriber;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\EntityEvent;
use LAG\AdminBundle\Event\Subscriber\AdminSubscriber;
use LAG\AdminBundle\Event\ViewEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\ActionFactory;
use LAG\AdminBundle\Factory\DataProviderFactory;
use LAG\AdminBundle\Factory\ViewFactory;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\View\ViewInterface;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class AdminSubscriberTest extends AdminTestBase
{
    /**
     * Test subscribed events.
     */
    public function testGetSubscribedEvents()
    {
        $events = AdminSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(AdminEvents::HANDLE_REQUEST, $events);
        $this->assertArrayHasKey(AdminEvents::VIEW, $events);
        $this->assertArrayHasKey(AdminEvents::ENTITY_LOAD, $events);
        $this->assertArrayHasKey(AdminEvents::ENTITY_SAVE, $events);
    }

    /**
     * Test the way the subscriber handle a request.
     */
    public function testHandleRequest()
    {
        $action = $this->getMockWithoutConstructor(ActionInterface::class);

        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);
        $actionFactory
            ->method('create')
            ->with('list', 'panda')
            ->willReturn($action)
        ;
        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $dataProviderFactory = $this->getMockWithoutConstructor(DataProviderFactory::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $session = $this->getMockWithoutConstructor(Session::class);
        $translator = $this->getMockWithoutConstructor(TranslatorInterface::class);

        $subscriber = new AdminSubscriber(
            $actionFactory,
            $viewFactory,
            $dataProviderFactory,
            $eventDispatcher,
            $session,
            $translator
        );

        $request = new Request([], [], [
            '_action' => 'list',
        ]);
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getName')
            ->willReturn('panda')
        ;
        $admin
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn(new AdminConfiguration(new ApplicationConfiguration()))
        ;
        $event = new AdminEvent($admin, $request);

        $subscriber->handleRequest($event);

        $this->assertEquals($action, $event->getAction());
    }

    /**
     * Test the way the subscriber handle a request without admin parameters.
     */
    public function testHandleRequestWithoutRequestParameter()
    {
        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);
        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $dataProviderFactory = $this->getMockWithoutConstructor(DataProviderFactory::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $session = $this->getMockWithoutConstructor(Session::class);
        $translator = $this->getMockWithoutConstructor(TranslatorInterface::class);

        $subscriber = new AdminSubscriber(
            $actionFactory,
            $viewFactory,
            $dataProviderFactory,
            $eventDispatcher,
            $session,
            $translator
        );

        $request = new Request();
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);

        $event = new AdminEvent($admin, $request);

        $this->assertExceptionRaised(Exception::class, function () use ($subscriber, $event) {
            $subscriber->handleRequest($event);
        });
    }

    /**
     * Test the view creation.
     */
    public function testCreateView()
    {
        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);
        $view = $this->getMockWithoutConstructor(ViewInterface::class);

        $dataProviderFactory = $this->getMockWithoutConstructor(DataProviderFactory::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->with('menus')
            ->willReturn([])
        ;

        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getName')
            ->willReturn('edit')
        ;
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);

        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('pandas')
        ;
        $admin
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
        $admin
            ->expects($this->once())
            ->method('getEntities')
            ->willReturn([
                'entity',
            ])
        ;
        $admin
            ->expects($this->once())
            ->method('getForms')
            ->willReturn([
                'form',
            ])
        ;
        $request = new Request();

        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $viewFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $request,
                'edit',
                'pandas',
                $adminConfiguration,
                $actionConfiguration,
                [
                    'entity',
                ],
                [
                    'form',
                ]
            )
            ->willReturn($view)
        ;

        $session = $this->getMockWithoutConstructor(Session::class);
        $translator = $this->getMockWithoutConstructor(TranslatorInterface::class);

        $subscriber = new AdminSubscriber(
            $actionFactory,
            $viewFactory,
            $dataProviderFactory,
            $eventDispatcher,
            $session,
            $translator
        );
        $event = new ViewEvent($admin, $request);

        $subscriber->createView($event);

        $this->assertEquals($view, $event->getView());
    }

    /**
     * Test entity loading with the none strategy.
     */
    public function testLoadEntitiesWithNoneStrategy()
    {
        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);
        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['load_strategy', LAGAdminBundle::LOAD_STRATEGY_NONE],
            ])
        ;

        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['data_provider', 'my_data_provider'],
                ['entity', 'MyClass'],
            ])
        ;

        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;

        $dataProvider = $this->getMockWithoutConstructor(DataProviderInterface::class);

        $dataProviderFactory = $this->getMockWithoutConstructor(DataProviderFactory::class);
        $dataProviderFactory
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('my_data_provider')
            ->willReturn($dataProvider)
        ;

        $session = $this->getMockWithoutConstructor(Session::class);
        $translator = $this->getMockWithoutConstructor(TranslatorInterface::class);

        $subscriber = new AdminSubscriber(
            $actionFactory,
            $viewFactory,
            $dataProviderFactory,
            $eventDispatcher,
            $session,
            $translator
        );

        $request = new Request();
        $event = new EntityEvent($admin, $request);

        $subscriber->loadEntities($event);

        $this->assertEquals(null, $event->getEntities());
    }

    /**
     * Test entity loading with the multiple strategy.
     */
    public function testLoadEntitiesWithMultipleStrategy()
    {
        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);
        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['load_strategy', LAGAdminBundle::LOAD_STRATEGY_MULTIPLE],
            ])
        ;

        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['data_provider', 'my_data_provider'],
                ['entity', 'MyClass'],
            ])
        ;

        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;

        $test = new stdClass();

        $dataProvider = $this->getMockWithoutConstructor(DataProviderInterface::class);
        $dataProvider
            ->expects($this->atLeastOnce())
            ->method('getCollection')
            ->with($admin, [])
            ->willReturn($test)
        ;

        $dataProviderFactory = $this->getMockWithoutConstructor(DataProviderFactory::class);
        $dataProviderFactory
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('my_data_provider')
            ->willReturn($dataProvider)
        ;
        $session = $this->getMockWithoutConstructor(Session::class);
        $translator = $this->getMockWithoutConstructor(TranslatorInterface::class);

        $subscriber = new AdminSubscriber(
            $actionFactory,
            $viewFactory,
            $dataProviderFactory,
            $eventDispatcher,
            $session,
            $translator
        );

        $request = new Request();
        $event = new EntityEvent($admin, $request);

        $subscriber->loadEntities($event);

        $this->assertEquals($test, $event->getEntities());
    }

    /**
     * Test entity loading with the unique strategy.
     */
    public function testLoadEntitiesWithUniqueStrategy()
    {
        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);
        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['load_strategy', LAGAdminBundle::LOAD_STRATEGY_UNIQUE],
                ['route_requirements', [
                    'id' => '~',
                ]],
            ])
        ;

        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['data_provider', 'my_data_provider'],
                ['entity', 'MyClass'],
            ])
        ;

        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;

        $test = new stdClass();

        $dataProvider = $this->getMockWithoutConstructor(DataProviderInterface::class);
        $dataProvider
            ->expects($this->atLeastOnce())
            ->method('getItem')
            ->with($admin, 42)
            ->willReturn($test)
        ;

        $dataProviderFactory = $this->getMockWithoutConstructor(DataProviderFactory::class);
        $dataProviderFactory
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('my_data_provider')
            ->willReturn($dataProvider)
        ;
        $session = $this->getMockWithoutConstructor(Session::class);
        $translator = $this->getMockWithoutConstructor(TranslatorInterface::class);

        $subscriber = new AdminSubscriber(
            $actionFactory,
            $viewFactory,
            $dataProviderFactory,
            $eventDispatcher,
            $session,
            $translator
        );

        $request = new Request([
            'id' => 42,
        ]);
        $event = new EntityEvent($admin, $request);

        $subscriber->loadEntities($event);

        $this->assertEquals($test, $event->getEntities()->first());
    }

    /**
     * Test entity loading without identifier parameter.
     */
    public function testLoadEntitiesWithoutIdentifier()
    {
        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);
        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['load_strategy', LAGAdminBundle::LOAD_STRATEGY_UNIQUE],
                ['route_requirements', [
                    'id' => '~',
                ]],
            ])
        ;

        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['data_provider', 'my_data_provider'],
                ['entity', 'MyClass'],
            ])
        ;

        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;

        $dataProvider = $this->getMockWithoutConstructor(DataProviderInterface::class);
        $dataProvider
            ->expects($this->never())
            ->method('getItem')
        ;

        $dataProviderFactory = $this->getMockWithoutConstructor(DataProviderFactory::class);
        $dataProviderFactory
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('my_data_provider')
            ->willReturn($dataProvider)
        ;
        $session = $this->getMockWithoutConstructor(Session::class);
        $translator = $this->getMockWithoutConstructor(TranslatorInterface::class);

        $subscriber = new AdminSubscriber(
            $actionFactory,
            $viewFactory,
            $dataProviderFactory,
            $eventDispatcher,
            $session,
            $translator
        );

        $request = new Request();
        $event = new EntityEvent($admin, $request);

        $this->assertExceptionRaised(Exception::class, function () use ($subscriber, $event) {
            $subscriber->loadEntities($event);
        });
    }

    /**
     * Test the save item process.
     */
    public function testSaveEntity()
    {
        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);
        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['data_provider', 'my_data_provider'],
                ['entity', 'MyClass'],
                ['translation_pattern', 'test.{admin}.{key}'],
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
            ->willReturn('stefany')
        ;

        $dataProvider = $this->getMockWithoutConstructor(DataProviderInterface::class);
        $dataProvider
            ->expects($this->atLeastOnce())
            ->method('saveItem')
            ->with($admin)
        ;

        $dataProviderFactory = $this->getMockWithoutConstructor(DataProviderFactory::class);
        $dataProviderFactory
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('my_data_provider')
            ->willReturn($dataProvider)
        ;
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $bag = $this->getMockWithoutConstructor(FlashBag::class);
        $bag
            ->expects($this->atLeastOnce())
            ->method('add')
            ->with('success', 'Save')
        ;

        $session = $this->getMockWithoutConstructor(Session::class);
        $session
            ->expects($this->atLeastOnce())
            ->method('getFlashBag')
            ->willReturn($bag)
        ;

        $translator = $this->getMockWithoutConstructor(TranslatorInterface::class);
        $translator
            ->expects($this->atLeastOnce())
            ->method('trans')
            ->with('test.stefany.save_success')
            ->willReturn('Save')
        ;

        $subscriber = new AdminSubscriber(
            $actionFactory,
            $viewFactory,
            $dataProviderFactory,
            $eventDispatcher,
            $session,
            $translator
        );

        $request = new Request([], [], [
            '_action' => 'list',
        ]);

        $event = new EntityEvent($admin, $request);

        $subscriber->saveEntity($event);
    }
}
