<?php

namespace LAG\AdminBundle\Tests\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
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
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminTest extends AdminTestBase
{
    /**
     * @dataProvider getHandleRequestProvider
     */
    public function testHandleRequest($entities)
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
            ->willReturnCallback(function($eventName, $event) use ($action, $form, $entities) {
                if (Events::ADMIN_HANDLE_REQUEST === $eventName) {
                    /** @var AdminEvent $event */
                    $this->assertInstanceOf(AdminEvent::class, $event);
                    $event->setAction($action);
                }

                if (Events::ENTITY_LOAD === $eventName) {
                    /** @var EntityEvent $event */
                    $this->assertInstanceOf(EntityEvent::class, $event);
                    $event->setEntities(new ArrayCollection($entities));
                }

                if (Events::ADMIN_CREATE_FORM === $eventName) {
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

        $this->assertExceptionRaised(Exception::class, function () use ($admin) {
            $admin->getRequest();
        });

        $admin->handleRequest($request);

        $this->assertEquals('admin_test', $admin->getName());
        $this->assertEquals($resource, $admin->getResource());
        $this->assertEquals($eventDispatcher, $admin->getEventDispatcher());
        $this->assertEquals($configuration, $admin->getConfiguration());
        $this->assertCount(1, $admin->getEntities());

        if (count($entities) > 0) {
            $this->assertEquals($admin->getEntities()[0], 'test');
        }
        $this->assertEquals($request, $admin->getRequest());
        $this->assertTrue($admin->hasForm('entity'));
        $this->assertCount(1, $admin->getForms());
        $this->assertEquals($form, $admin->getForms()['entity']);
        $this->assertEquals($form, $admin->getForm('entity'));
        $this->assertExceptionRaised(Exception::class, function () use ($admin) {
            $admin->getForm('invalid');
        });
    }

    public function getHandleRequestProvider(): array
    {
        return [
            [['test',],],
            [[]]
        ];
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

        $this->assertExceptionRaised(Exception::class, function() use ($admin, $request) {
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
            ->willReturnCallback(function($eventName, $event) use ($view) {
                $this->assertEquals(Events::ADMIN_VIEW, $eventName);
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

    /**
     * @expectedException \LAG\AdminBundle\Exception\Exception
     */
    public function testCreateViewWithoutRequest()
    {
        list($admin) = $this->createAdmin();

        $admin->createView();
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

        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->exactly(5))
            ->method('dispatch')
            ->willReturnCallback(function($eventName, $event) use ($action) {
                if (Events::ADMIN_HANDLE_REQUEST === $eventName) {
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
            ->willReturnCallback(function($eventName, $event) use ($action, $form) {
                if (Events::ADMIN_HANDLE_REQUEST === $eventName) {
                    /** @var AdminEvent $event */
                    $this->assertInstanceOf(AdminEvent::class, $event);
                    $event->setAction($action);
                }

                if (Events::ENTITY_LOAD === $eventName) {
                    /** @var EntityEvent $event */
                    $this->assertInstanceOf(EntityEvent::class, $event);
                    $event->setEntities(new ArrayCollection());
                }

                if (Events::ADMIN_CREATE_FORM === $eventName) {
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

    public function testGetEntityClass(): void
    {
        $resource = $this->createMock(AdminResource::class);
        $configuration = $this->createMock(AdminConfiguration::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);

        $configuration
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('entity')
            ->willReturn('MyClass')
        ;

        $admin = new Admin(
            $resource,
            $configuration,
            $eventDispatcher
        );
        $this->assertEquals('MyClass', $admin->getEntityClass());
    }

    /**
     * @return MockObject[]|AdminInterface[]
     */
    protected function createAdmin(): array
    {
        $resource = $this->createMock(AdminResource::class);
        $configuration = $this->createMock(AdminConfiguration::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);

        $admin = new Admin(
            $resource,
            $configuration,
            $eventDispatcher
        );

        return [
            $admin,
            $resource,
            $configuration,
            $eventDispatcher,
        ];
    }
}
