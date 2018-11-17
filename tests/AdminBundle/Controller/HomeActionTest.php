<?php

namespace LAG\AdminBundle\Tests\Controller;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Controller\HomeAction;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\MenuEvent;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class HomeActionTest extends AdminTestBase
{
    public function testInvoke()
    {
        $twig = $this->getMockWithoutConstructor(\Twig_Environment::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('my_template')
            ->willReturn('content')
        ;
        $configuration = $this->getMockWithoutConstructor(ApplicationConfiguration::class);
        $configuration
            ->expects($this->once())
            ->method('getParameter')
            ->with('homepage_template')
            ->willReturn('my_template')
        ;

        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($eventName, $event) {
                $this->assertEquals(Events::MENU, $eventName);
                $this->assertInstanceOf(MenuEvent::class, $event);
            })
        ;
        $storage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);
        $storage
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;

        $controller = new HomeAction(
            $twig,
            $eventDispatcher,
            $storage
        );
        $response = $controller->__invoke();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('content', $response->getContent());
    }
}
