<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Request;

use Exception;
use LAG\AdminBundle\Admin\Registry\Registry;
use LAG\AdminBundle\Admin\Request\RequestHandler;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\HttpFoundation\Request;

class RequestHandlerTest extends AdminTestBase
{
    public function testSupports()
    {
        $registry = $this->getMockWithoutConstructor(Registry::class);
        $handler = new RequestHandler($registry);

        // test request without route params
        $request = new Request();
        $this->assertFalse($handler->supports($request));

        // test request with empty route params
        $handler = new RequestHandler($registry);
        $request = new Request([], [], [
            '_route_params' => [
            ],
        ]);
        $this->assertFalse($handler->supports($request));

        // test request with one route params
        $handler = new RequestHandler($registry);
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'list',
            ],
        ]);
        $this->assertFalse($handler->supports($request));

        // test request with one route params
        $handler = new RequestHandler($registry);
        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'list',
            ],
        ]);
        $this->assertFalse($handler->supports($request));

        // test request with missing admin
        $registry = $this->getMockWithoutConstructor(Registry::class);
        $registry
            ->expects($this->once())
            ->method('has')
            ->with('wookie')
            ->willReturn(false)
        ;
        $handler = new RequestHandler($registry);
        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'wookie',
                '_action' => 'list',
            ],
        ]);
        $this->assertFalse($handler->supports($request));

        // test request with correct route params
        $registry = $this->getMockWithoutConstructor(Registry::class);
        $registry
            ->expects($this->once())
            ->method('has')
            ->with('planet')
            ->willReturn(true)
        ;
        $handler = new RequestHandler($registry);
        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'planet',
                '_action' => 'list',
            ],
        ]);
        $this->assertTrue($handler->supports($request));
    }

    public function testHandle()
    {
        $registry = $this->getMockWithoutConstructor(Registry::class);
        $registry
            ->expects($this->once())
            ->method('get')
            ->with('wookie')
        ;

        $handler = new RequestHandler($registry);

        $this->assertExceptionRaised(Exception::class, function () use ($handler) {
            $request = new Request();
            $handler->handle($request);
        });

        $this->assertExceptionRaised(Exception::class, function () use ($handler) {
            $request = new Request([], [], [
                '_route_params' => [
                    '_action' => 'list',
                ],
            ]);
            $handler->handle($request);
        });

        $this->assertExceptionRaised(Exception::class, function () use ($handler) {
            $request = new Request([], [], [
                '_route_params' => [
                    '_admin' => 'wookie',
                ],
            ]);
            $handler->handle($request);
        });

        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'wookie',
                '_action' => 'list',
            ],
        ]);
        $handler->handle($request);
    }
}
