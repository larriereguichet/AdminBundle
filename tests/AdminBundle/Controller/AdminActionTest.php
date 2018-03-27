<?php

namespace LAG\AdminBundle\Tests\Controller;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Controller\AdminAction;
use LAG\AdminBundle\Factory\AdminFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\View\RedirectView;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminActionTest extends AdminTestBase
{
    public function testInvoke()
    {
        $view = $this->getMockWithoutConstructor(ViewInterface::class);
        $view
            ->expects($this->once())
            ->method('getTemplate')
            ->willReturn('my_template')
        ;
        $twig = $this->getMockWithoutConstructor(\Twig_Environment::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('my_template', [
                'admin' => $view,
            ])
            ->willReturn('content')
        ;
        $request = new Request();


        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $admin
            ->expects($this->once())
            ->method('createView')
            ->willReturn($view)
        ;

        $adminFactory = $this->getMockWithoutConstructor(AdminFactory::class);
        $adminFactory
            ->expects($this->once())
            ->method('createFromRequest')
            ->with($request)
            ->willReturn($admin)
        ;

        $controller = new AdminAction(
            $adminFactory,
            $twig
        );
        $response = $controller->__invoke($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('content', $response->getContent());
    }

    public function testInvokeWithRedirection()
    {
        $view = $this->getMockWithoutConstructor(RedirectView::class);
        $view
            ->expects($this->once())
            ->method('getUrl')
            ->willReturn('/admin/home')
        ;
        $twig = $this->getMockWithoutConstructor(\Twig_Environment::class);
        $request = new Request();

        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $admin
            ->expects($this->once())
            ->method('createView')
            ->willReturn($view)
        ;
        $adminFactory = $this->getMockWithoutConstructor(AdminFactory::class);
        $adminFactory
            ->expects($this->once())
            ->method('createFromRequest')
            ->with($request)
            ->willReturn($admin)
        ;
        $controller = new AdminAction(
            $adminFactory,
            $twig
        );
        $response = $controller->__invoke($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/admin/home', $response->getTargetUrl());
    }
}
