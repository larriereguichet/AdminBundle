<?php

namespace LAG\AdminBundle\Tests\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Resource\AdminResource;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Event\Events\RequestEvent;
use LAG\AdminBundle\Event\Events\ViewEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\View\ViewInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminTest extends TestCase
{
    private Admin $admin;
    private MockObject $resource;
    private MockObject $configuration;
    private MockObject $eventDispatcher;

    /**
     * @dataProvider getHandleRequestProvider
     */
    public function testHandleRequest($entities)
    {
        $action = $this->createMock(ActionInterface::class);
        $form = $this->createMock(FormInterface::class);

        $this->eventDispatcher
            ->expects($this->exactly(4))
            ->method('dispatch')
            ->willReturnCallback(function ($event, $eventName) use ($action, $form, $entities) {
                if (AdminEvents::ADMIN_REQUEST === $eventName) {
                    $this->assertInstanceOf(RequestEvent::class, $event);
                    $event->setAction($action);
                }

                if (AdminEvents::ADMIN_DATA === $eventName) {
                    $this->assertInstanceOf(DataEvent::class, $event);
                    $event->setData(new ArrayCollection($entities));
                }

                if (AdminEvents::ADMIN_FORM === $eventName) {
                    $this->assertInstanceOf(FormEvent::class, $event);
                    $event->addForm('entity', $form);
                }

                return $event;
            })
        ;

        $request = new Request();

        $this->assertExceptionRaised(Exception::class, function () {
            $this->admin->getRequest();
        });

        $this->admin->handleRequest($request);

        $this->assertEquals('admin_test', $this->admin->getName());
        $this->assertEquals($this->resource, $this->admin->getResource());
        $this->assertEquals($this->eventDispatcher, $this->admin->getEventDispatcher());
        $this->assertEquals($this->configuration, $this->admin->getConfiguration());
        $this->assertCount(count($entities), $this->admin->getData());

        if (count($entities) > 0) {
            $this->assertEquals('test', $this->admin->getData()[0]);
        }
        $this->assertEquals($request, $this->admin->getRequest());
        $this->assertTrue($this->admin->hasForm('entity'));
        $this->assertCount(1, $this->admin->getForms());
        $this->assertEquals($form, $this->admin->getForms()['entity']);
        $this->assertEquals($form, $this->admin->getForm('entity'));
        $this->assertExceptionRaised(Exception::class, function () {
            $this->admin->getForm('invalid');
        });
    }

    public function getHandleRequestProvider(): array
    {
        return [
            [['test']],
            [[]],
        ];
    }

    public function testHandleRequestWithoutAction()
    {
        $this->eventDispatcher
            ->expects($this->exactly(1))
            ->method('dispatch')
        ;
        $request = new Request();

        $this->assertExceptionRaised(Exception::class, function () use ($request) {
            $this->admin->handleRequest($request);
        });
    }

    public function testCreateView()
    {
        $view = $this->createMock(ViewInterface::class);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($event, $eventName) use ($view) {
                $this->assertEquals(AdminEvents::ADMIN_VIEW, $eventName);
                $this->assertInstanceOf(ViewEvent::class, $event);

                $event->setView($view);

                return $event;
            })
        ;
        $request = new Request();
        $this->setPrivateProperty($this->admin, 'request', $request);

        $createdView = $this->admin->createView();
        $this->assertInstanceOf(ViewInterface::class, $createdView);
        $this->assertEquals($view, $createdView);
    }

    public function testCreateViewWithoutRequest()
    {
        $this->expectException(Exception::class);

        $this->admin->createView();
    }

    public function testGetAction()
    {
        $action = $this->createMock(ActionInterface::class);
        $this->setPrivateProperty($this->admin, 'action', $action);

        $this->assertEquals($action, $this->admin->getAction());
        $this->assertTrue($this->admin->hasAction());
    }

    public function testHandleFormWithoutForm()
    {
        $resource = $this->createMock(AdminResource::class);
        $configuration = $this->createMock(AdminConfiguration::class);
        $action = $this->createMock(ActionInterface::class);

        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->exactly(4))
            ->method('dispatch')
            ->willReturnCallback(function ($event, $eventName) use ($action) {
                $this->assertContains($eventName, [
                    AdminEvents::ADMIN_REQUEST,
                    AdminEvents::ADMIN_DATA,
                    AdminEvents::ADMIN_FORM,
                    AdminEvents::ADMIN_HANDLE_FORM,
                    AdminEvents::ADMIN_VIEW,
                ]);

                if ($eventName === AdminEvents::ADMIN_REQUEST) {
                    $event->setAction($action);
                }

                return $event;
            })
        ;

        $admin = new Admin(
            $resource,
            $configuration,
            $eventDispatcher
        );
        $request = new Request();

        $admin->handleRequest($request);
    }

    public function testHandleFormWithoutEntities()
    {
        $action = $this->createMock(ActionInterface::class);
        $form = $this->createMock(FormInterface::class);

        $this->eventDispatcher
            ->expects($this->exactly(4))
            ->method('dispatch')
            ->willReturnCallback(function ($event, $eventName) use ($action, $form) {
                if (AdminEvents::ADMIN_REQUEST === $eventName) {
                    $this->assertInstanceOf(RequestEvent::class, $event);
                    $event->setAction($action);
                }

                if (AdminEvents::ADMIN_DATA === $eventName) {
                    $this->assertInstanceOf(DataEvent::class, $event);
                    $event->setData(null);
                }

                if (AdminEvents::ADMIN_FORM === $eventName) {
                    $this->assertInstanceOf(FormEvent::class, $event);
                    $event->addForm('entity', $form);
                }

                return $event;
            })
        ;
        $request = new Request();
        $this->admin->handleRequest($request);
    }

    public function testGetEntityClass(): void
    {
        $this->configuration
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('entity')
            ->willReturn('MyClass')
        ;
        $this->assertEquals('MyClass', $this->admin->getEntityClass());
    }

    protected function setUp(): void
    {
        $this->resource = $this->createMock(AdminResource::class);
        $this->resource
            ->expects($this->once())
            ->method('getName')
            ->willReturn('admin_test')
        ;
        $this->configuration = $this->createMock(AdminConfiguration::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        $this->admin = new Admin($this->resource, $this->configuration, $this->eventDispatcher);
    }
}
