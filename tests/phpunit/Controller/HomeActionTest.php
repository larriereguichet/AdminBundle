<?php

namespace LAG\AdminBundle\Tests\Controller;

use LAG\AdminBundle\Controller\HomeAction;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HomeActionTest extends TestCase
{
    public function testInvoke()
    {
        $twig = $this->createMock(Environment::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('@LAGAdmin/pages/home.html.twig')
            ->willReturn('content')
        ;
        $controller = new HomeAction($twig);
        $response = $controller->__invoke();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('content', $response->getContent());
    }
}
