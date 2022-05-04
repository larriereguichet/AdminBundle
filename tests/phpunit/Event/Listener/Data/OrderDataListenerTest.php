<?php

namespace LAG\AdminBundle\Tests\Event\Listener\Data;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\ActionConfiguration;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Event\Listener\Data\OrderRequestDataListener;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class OrderDataListenerTest extends TestCase
{
    public function testInvokeWithSort(): void
    {
        [$listener] = $this->createListener();

        $request = new Request(['sort' => 'name']);
        $admin = $this->createMock(AdminInterface::class);
        $action = $this->createMock(ActionInterface::class);
        $actionConfiguration = $this->createMock(ActionConfiguration::class);

        $dataEvent = $this->createMock(DataEvent::class);
        $dataEvent
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;
        $dataEvent
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;
        $dataEvent
            ->expects($this->once())
            ->method('addOrderBY')
            ->with('name', 'asc')
        ;

        $listener->__invoke($dataEvent);
    }

    public function testInvokeWithoutSort(): void
    {
        [$listener] = $this->createListener();

        $request = new Request();
        $admin = $this->createMock(AdminInterface::class);
        $action = $this->createMock(ActionInterface::class);
        $actionConfiguration = $this->createMock(ActionConfiguration::class);

        $dataEvent = $this->createMock(DataEvent::class);
        $dataEvent
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;
        $dataEvent
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn(['title' => 'desc'])
        ;
        $dataEvent
            ->expects($this->once())
            ->method('addOrderBY')
            ->with('title', 'desc')
        ;

        $listener->__invoke($dataEvent);
    }

    /**
     * @return OrderRequestDataListener[]
     */
    private function createListener(): array
    {
        $listener = new OrderRequestDataListener();

        return [$listener];
    }
}
