<?php

namespace LAG\AdminBundle\Tests\Event\Subscriber;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Event\Events\AdminEvent;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\EntityEvent;
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
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class AdminSubscriberTest extends AdminTestBase
{
    /**
     * Test if the service declaration is correct.
     */
    public function testServiceExists()
    {
        $this->assertServiceExists(AdminSubscriber::class);
    }

    public function testMethodsExists()
    {
        list($subscriber) = $this->createSubscriber();

        $this->assertSubscribedMethodsExists($subscriber);
    }

    /**
     * Test subscribed events.
     */
    public function testGetSubscribedEvents()
    {
        $events = AdminSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::HANDLE_REQUEST, $events);
        $this->assertArrayHasKey(Events::VIEW, $events);
        $this->assertArrayHasKey(Events::ENTITY_LOAD, $events);
        $this->assertArrayHasKey(Events::ENTITY_SAVE, $events);
    }

    /**
     * Test the way the subscriber handle a request.
     */
    public function testHandleRequest()
    {
        list($subscriber, $actionFactory) = $this->createSubscriber();

        $action = $this->createMock(ActionInterface::class);
        $actionFactory
            ->method('create')
            ->with('list', 'panda')
            ->willReturn($action)
        ;
        $request = new Request([], [], [
            '_action' => 'list',
        ]);
        $admin = $this->createMock(AdminInterface::class);
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
        list($subscriber) = $this->createSubscriber();

        $request = new Request();
        $admin = $this->createMock(AdminInterface::class);
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
        list($subscriber, , $viewFactory) = $this->createSubscriber();
        $view = $this->createMock(ViewInterface::class);

        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->with('menus')
            ->willReturn([])
        ;

        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('edit')
        ;
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $adminConfiguration = $this->createMock(AdminConfiguration::class);

        $admin = $this->createMock(AdminInterface::class);
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
        $event = new ViewEvent($admin, $request);
        $subscriber->createView($event);

        $this->assertEquals($view, $event->getView());
    }

    /**
     * Test entity loading with the none strategy.
     */
    public function testLoadEntitiesWithNoneStrategy()
    {
        list($subscriber) = $this->createSubscriber();

        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['load_strategy', LAGAdminBundle::LOAD_STRATEGY_NONE],
            ])
        ;

        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['data_provider', 'my_data_provider'],
                ['entity', 'MyClass'],
            ])
        ;

        $admin = $this->createMock(AdminInterface::class);
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
        list($subscriber, , , $dataProviderFactory, , , ,) = $this->createSubscriber();

        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['load_strategy', LAGAdminBundle::LOAD_STRATEGY_MULTIPLE],
            ])
        ;

        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['data_provider', 'my_data_provider'],
                ['entity', 'MyClass'],
            ])
        ;

        $admin = $this->createMock(AdminInterface::class);
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

        $dataProvider = $this->createMock(DataProviderInterface::class);
        $dataProvider
            ->expects($this->atLeastOnce())
            ->method('getCollection')
            ->with($admin, [])
            ->willReturn($test)
        ;
        $dataProviderFactory
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('my_data_provider')
            ->willReturn($dataProvider)
        ;


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
        list($subscriber, , , $dataProviderFactory) = $this->createSubscriber();

        $actionConfiguration = $this->createMock(ActionConfiguration::class);
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

        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['data_provider', 'my_data_provider'],
                ['entity', 'MyClass'],
            ])
        ;

        $admin = $this->createMock(AdminInterface::class);
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

        $dataProvider = $this->createMock(DataProviderInterface::class);
        $dataProvider
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with($admin, 42)
            ->willReturn($test)
        ;
        $dataProviderFactory
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('my_data_provider')
            ->willReturn($dataProvider)
        ;
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
        list($subscriber, , , $dataProviderFactory) = $this->createSubscriber();

        $actionConfiguration = $this->createMock(ActionConfiguration::class);
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

        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['data_provider', 'my_data_provider'],
                ['entity', 'MyClass'],
            ])
        ;

        $admin = $this->createMock(AdminInterface::class);
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

        $dataProvider = $this->createMock(DataProviderInterface::class);
        $dataProvider
            ->expects($this->never())
            ->method('get')
        ;
        $dataProviderFactory
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('my_data_provider')
            ->willReturn($dataProvider)
        ;
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
        list($subscriber, , , $dataProviderFactory, , $session, $translator) = $this->createSubscriber();

        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['data_provider', 'my_data_provider'],
                ['entity', 'MyClass'],
                ['translation_pattern', 'test.{admin}.{key}'],
            ])
        ;

        $admin = $this->createMock(AdminInterface::class);
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

        $dataProvider = $this->createMock(DataProviderInterface::class);
        $dataProvider
            ->expects($this->atLeastOnce())
            ->method('save')
            ->with($admin)
        ;

        $dataProviderFactory
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('my_data_provider')
            ->willReturn($dataProvider)
        ;

        $bag = $this->createMock(FlashBag::class);
        $bag
            ->expects($this->atLeastOnce())
            ->method('add')
            ->with('success', 'Save')
        ;

        $session
            ->expects($this->atLeastOnce())
            ->method('getFlashBag')
            ->willReturn($bag)
        ;

        $translator
            ->expects($this->atLeastOnce())
            ->method('trans')
            ->with('test.stefany.save_success')
            ->willReturn('Save')
        ;

        $request = new Request([], [], [
            '_action' => 'list',
        ]);

        $event = new EntityEvent($admin, $request);

        $subscriber->saveEntity($event);
    }

    private function createSubscriber()
    {
        $actionFactory = $this->createMock(ActionFactory::class);
        $viewFactory = $this->createMock(ViewFactory::class);
        $dataProviderFactory = $this->createMock(DataProviderFactory::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $session = $this->createMock(Session::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $router = $this->createMock(RouterInterface::class);

        $subscriber = new AdminSubscriber(
            $actionFactory,
            $viewFactory,
            $dataProviderFactory,
            $eventDispatcher,
            $session,
            $translator,
            $router
        );

        return [
            $subscriber,
            $actionFactory,
            $viewFactory,
            $dataProviderFactory,
            $eventDispatcher,
            $session,
            $translator,
            $router
        ];
    }
}
