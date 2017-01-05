<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
use LAG\AdminBundle\Admin\Request\RequestHandler;
use LAG\AdminBundle\Event\Subscriber\KernelSubscriber;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class KernelSubscriberTest extends AdminTestBase
{
    public function testSubscribedEvents()
    {
        $this->assertEquals([
            'kernel.controller' => 'onKernelController',
            'kernel.request' => 'onKernelRequest',
        ], KernelSubscriber::getSubscribedEvents());
    }

    /**
     * On KernelController event, we must inject the current Admin and ActionConfiguration into the Controller.
     */
    public function testOnKernelController()
    {
        $request = new Request([], [
            '_route_params' => [
                '_action' => 'list',
            ],
        ]);

        $requestHandler = $this->getMockWithoutConstructor(RequestHandler::class);
        $requestHandler
            ->expects($this->once())
            ->method('supports')
            ->willReturn(function($givenRequest) use ($request) {
                $this->assertEquals($givenRequest, $request);
                return true;
            })
        ;
        $controller = $this->getMockWithoutConstructor(ActionInterface::class);

        $event = $this->getMockWithoutConstructor(FilterControllerEvent::class);
        $event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturnCallback(function() use ($request) {
                return $request;
            })
        ;
        $event
            ->expects($this->once())
            ->method('getController')
            ->willReturnCallback(function() use ($controller) {
                return $controller;
            })
        ;

        $adminFactory = $this->getMockWithoutConstructor(AdminFactory::class);
        $adminFactory
            ->expects($this->once())
            ->method('injectAdmin')
        ;
        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);
        $actionFactory
            ->expects($this->once())
            ->method('injectConfiguration')
        ;

        $subscriber = new KernelSubscriber(
            $adminFactory,
            $actionFactory,
            $requestHandler
        );
        $subscriber->onKernelController($event);
    }
    
    public function testOnKernelRequest()
    {
        $requestHandler = $this->getMockWithoutConstructor(RequestHandler::class);
        
        $adminFactory = $this->getMockWithoutConstructor(AdminFactory::class);
        $adminFactory
            ->expects($this->once())
            ->method('init')
        ;
        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);

        $subscriber = new KernelSubscriber(
            $adminFactory,
            $actionFactory,
            $requestHandler
        );
        $subscriber->onKernelRequest();
    }
}
