<?php

namespace LAG\AdminBundle\Tests\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\Events\AdminEvent;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\EntityEvent;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Event\Events\ViewEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\AdminResource;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminTest extends AdminTestBase
{
    public function testHandleRequest()
    {
        $resource = $this->createMock(AdminResource::class);
        $resource
            ->expects($this->once())
            ->method('getName')
            ->willReturn('admin_test')
        ;
        $configuration = $this->createMock(AdminConfiguration::class);
        $action = $this->createMock(ActionInterface::class);
        $form = $this->createMock(FormInterface::class);
        $form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;
        $form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;

        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->exactly(6))
            ->method('dispatch')
            ->willReturnCallback(function ($eventName, $event) use ($action, $form) {
                if (Events::HANDLE_REQUEST === $eventName) {
                    /** @var AdminEvent $event */
                    $this->assertInstanceOf(AdminEvent::class, $event);
                    $event->setAction($action);
                }

                if (Events::ENTITY_LOAD === $eventName) {
                    /** @var EntityEvent $event */
                    $this->assertInstanceOf(EntityEvent::class, $event);
                    $event->setEntities(new ArrayCollection([
                        'test',
                    ]));
                }

                if (Events::CREATE_FORM === $eventName) {
                    /** @var FormEvent $event */
                    $this->assertInstanceOf(FormEvent::class, $event);
                    $event->addForm($form, 'entity');
                }
            })
        ;

        $admin = new Admin(
            $resource,
            $configuration,
            $eventDispatcher
        );
        $request = new Request();

        $admin->handleRequest($request);

        $this->assertEquals('admin_test', $admin->getName());
        $this->assertEquals($resource, $admin->getResource());
        $this->assertEquals($eventDispatcher, $admin->getEventDispatcher());
        $this->assertEquals($configuration, $admin->getConfiguration());
        $this->assertCount(1, $admin->getEntities());
        $this->assertEquals($admin->getEntities()[0], 'test');
        $this->assertCount(1, $admin->getForms());
        $this->assertEquals($form, $admin->getForms()['entity']);
    }

    public function testHandleRequestWithoutAction()
    {
        $resource = $this->createMock(AdminResource::class);
        $resource
            ->expects($this->once())
            ->method('getName')
            ->willReturn('admin_test')
        ;
        $configuration = $this->createMock(AdminConfiguration::class);

        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->exactly(1))
            ->method('dispatch')
        ;

        $admin = new Admin(
            $resource,
            $configuration,
            $eventDispatcher
        );
        $request = new Request();

        $this->assertExceptionRaised(Exception::class, function () use ($admin, $request) {
            $admin->handleRequest($request);
        });
    }

    public function testCreateView()
    {
        $resource = $this->createMock(AdminResource::class);
        $configuration = $this->createMock(AdminConfiguration::class);
        $view = $this->createMock(ViewInterface::class);

        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($eventName, $event) use ($view) {
                $this->assertEquals(Events::VIEW, $eventName);
                /** @var ViewEvent $event */
                $this->assertInstanceOf(ViewEvent::class, $event);

                $event->setView($view);
            })
        ;

        $admin = new Admin(
            $resource,
            $configuration,
            $eventDispatcher
        );
        $request = new Request();
        $this->setPrivateProperty($admin, 'request', $request);

        $createdView = $admin->createView();
        $this->assertInstanceOf(ViewInterface::class, $createdView);
        $this->assertEquals($view, $createdView);
    }

    public function testGetAction()
    {
        $resource = $this->createMock(AdminResource::class);
        $configuration = $this->createMock(AdminConfiguration::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);

        $admin = new Admin(
            $resource,
            $configuration,
            $eventDispatcher
        );
        $action = $this->createMock(ActionInterface::class);
        $this->setPrivateProperty($admin, 'action', $action);

        $this->assertEquals($action, $admin->getAction());
        $this->assertTrue($admin->hasAction());
    }

    public function testHandleFormWithoutForm()
    {
        $resource = $this->createMock(AdminResource::class);
        $configuration = $this->createMock(AdminConfiguration::class);
        $action = $this->createMock(ActionInterface::class);
        $form = $this->createMock(FormInterface::class);

        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->exactly(5))
            ->method('dispatch')
            ->willReturnCallback(function ($eventName, $event) use ($action, $form) {
                if (Events::HANDLE_REQUEST === $eventName) {
                    /** @var AdminEvent $event */
                    $this->assertInstanceOf(AdminEvent::class, $event);
                    $event->setAction($action);
                }
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
        $resource = $this->createMock(AdminResource::class);
        $configuration = $this->createMock(AdminConfiguration::class);
        $action = $this->createMock(ActionInterface::class);
        $form = $this->createMock(FormInterface::class);

        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->exactly(5))
            ->method('dispatch')
            ->willReturnCallback(function ($eventName, $event) use ($action, $form) {
                if (Events::HANDLE_REQUEST === $eventName) {
                    /** @var AdminEvent $event */
                    $this->assertInstanceOf(AdminEvent::class, $event);
                    $event->setAction($action);
                }

                if (Events::ENTITY_LOAD === $eventName) {
                    /** @var EntityEvent $event */
                    $this->assertInstanceOf(EntityEvent::class, $event);
                    $event->setEntities(new ArrayCollection());
                }

                if (Events::CREATE_FORM === $eventName) {
                    /** @var FormEvent $event */
                    $this->assertInstanceOf(FormEvent::class, $event);
                    $event->addForm($form, 'entity');
                }
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
}
