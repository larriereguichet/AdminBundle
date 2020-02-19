<?php

namespace LAG\AdminBundle\Tests\Controller;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Controller\HomeAction;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\OldBuildMenuEvent;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HomeActionTest extends AdminTestBase
{
    public function testInvoke()
    {
        $twig = $this->createMock(Environment::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('my_template')
            ->willReturn('content')
        ;
        $configuration = $this->createMock(ApplicationConfiguration::class);
        $configuration
            ->expects($this->once())
            ->method('getParameter')
            ->with('homepage_template')
            ->willReturn('my_template')
        ;

        $storage = $this->createMock(ApplicationConfigurationStorage::class);
        $storage
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;

        $controller = new HomeAction(
            $twig,
            $storage
        );
        $response = $controller->__invoke();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('content', $response->getContent());
    }
}
