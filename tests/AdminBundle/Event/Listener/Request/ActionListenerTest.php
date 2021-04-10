<?php

namespace LAG\AdminBundle\Tests\Event\Listener\Request;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\Events\RequestEvent;
use LAG\AdminBundle\Event\Listener\Request\ActionListener;
use LAG\AdminBundle\Factory\ActionFactoryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;

class ActionListenerTest extends TestCase
{
    private ActionListener $listener;
    private MockObject $extractor;
    private MockObject $actionFactory;
    private MockObject $adminHelper;

    public function testInvoke(): void
    {
        $request = new Request([], [], ['_action' => 'my_action']);
        $admin = $this->createMock(AdminInterface::class);
        $adminConfiguration = $this->createMock(AdminConfiguration::class);

        $event = $this->createMock(RequestEvent::class);
        $event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;
        $event
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;

        $this
            ->extractor
            ->expects($this->once())
            ->method('getActionName')
            ->willReturn('my_action')
        ;

        $this
            ->adminHelper
            ->expects($this->once())
            ->method('setAdmin')
            ->with($admin)
        ;
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;

        $action = $this->createMock(ActionInterface::class);
        $this->actionFactory
            ->expects($this->once())
            ->method('create')
            ->with('my_action')
            ->willReturn($action)
        ;

        $event
            ->expects($this->once())
            ->method('setAction')
            ->with($action)
        ;

        $this->listener->__invoke($event);
    }

    protected function setUp(): void
    {
        $this->actionFactory = $this->createMock(ActionFactoryInterface::class);
        $this->adminHelper = $this->createMock(AdminHelperInterface::class);
        $this->extractor = $this->createMock(ParametersExtractorInterface::class);
        $this->listener = new ActionListener($this->actionFactory, $this->adminHelper, $this->extractor);
    }
}
