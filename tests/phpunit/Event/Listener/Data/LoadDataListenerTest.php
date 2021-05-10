<?php

namespace LAG\AdminBundle\Tests\Event\Listener\Data;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\DataProvider\Registry\DataProviderRegistryInterface;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Event\Listener\Data\LoadDataListener;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\DataProvider\AdminAwareDataProviderInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\HttpFoundation\Request;

class LoadDataListenerTest extends TestCase
{
    public function testInvokeWithNoneStrategy(): void
    {
        [$listener] = $this->createListener();

        $request = new Request();

        $action = $this->createMock(ActionInterface::class);
        $admin = $this->createMock(AdminInterface::class);
        $actionConfiguration = $this->createMock(ActionConfiguration::class);

        $event = $this->createMock(DataEvent::class);
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
            ->method('getLoadStrategy')
            ->willReturn(AdminInterface::LOAD_STRATEGY_NONE)
        ;

        $listener->__invoke($event);
    }

    public function testInvokeWithStrategyUnique(): void
    {
        [$listener, $registry] = $this->createListener();

        $request = new Request(['id' => 666]);

        $action = $this->createMock(ActionInterface::class);
        $admin = $this->createMock(AdminInterface::class);
        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $dataProvider = $this->createMock(AdminAwareDataProviderInterface::class);

        $event = $this->createMock(DataEvent::class);
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

        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;

        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getLoadStrategy')
            ->willReturn(AdminInterface::LOAD_STRATEGY_UNIQUE)
        ;

        $adminConfiguration
            ->expects($this->once())
            ->method('getDataProvider')
            ->willReturn('my_provider_key')
        ;
        $registry
            ->expects($this->once())
            ->method('get')
            ->with('my_provider_key')
            ->willReturn($dataProvider)
        ;

        $dataProvider
            ->expects($this->once())
            ->method('setAdmin')
            ->with($admin)
        ;

        $actionConfiguration
            ->expects($this->once())
            ->method('getRouteParameters')
            ->willReturn(['id' => []])
        ;
        $admin
            ->expects($this->once())
            ->method('getEntityClass')
            ->willReturn('MyClass')
        ;

        $data = new stdClass();
        $data->test = true;

        $dataProvider
            ->expects($this->once())
            ->method('get')
            ->with('MyClass', 666)
            ->willReturn($data)
        ;
        $event
            ->expects($this->once())
            ->method('setData')
        ;

        $listener->__invoke($event);
    }

    public function testInvokeWithStrategyUniqueWithoutIdentifier(): void
    {
        [$listener, $registry] = $this->createListener();

        $request = new Request([]);

        $action = $this->createMock(ActionInterface::class);
        $admin = $this->createMock(AdminInterface::class);
        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $dataProvider = $this->createMock(AdminAwareDataProviderInterface::class);

        $event = $this->createMock(DataEvent::class);
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

        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;

        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getLoadStrategy')
            ->willReturn(AdminInterface::LOAD_STRATEGY_UNIQUE)
        ;

        $adminConfiguration
            ->expects($this->once())
            ->method('getDataProvider')
            ->willReturn('my_provider_key')
        ;
        $registry
            ->expects($this->once())
            ->method('get')
            ->with('my_provider_key')
            ->willReturn($dataProvider)
        ;

        $dataProvider
            ->expects($this->once())
            ->method('setAdmin')
            ->with($admin)
        ;

        $actionConfiguration
            ->expects($this->once())
            ->method('getRouteParameters')
            ->willReturn(['id' => []])
        ;

        $this->expectException(Exception::class);
        $listener->__invoke($event);
    }

    public function testInvokeWithStrategyMultiple(): void
    {
        [$listener, $registry] = $this->createListener();

        $request = new Request();

        $action = $this->createMock(ActionInterface::class);
        $admin = $this->createMock(AdminInterface::class);
        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $dataProvider = $this->createMock(AdminAwareDataProviderInterface::class);

        $event = $this->createMock(DataEvent::class);
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

        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;

        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getLoadStrategy')
            ->willReturn(AdminInterface::LOAD_STRATEGY_MULTIPLE)
        ;

        $adminConfiguration
            ->expects($this->once())
            ->method('getDataProvider')
            ->willReturn('my_provider_key')
        ;
        $registry
            ->expects($this->once())
            ->method('get')
            ->with('my_provider_key')
            ->willReturn($dataProvider)
        ;

        $dataProvider
            ->expects($this->once())
            ->method('setAdmin')
            ->with($admin)
        ;

        $actionConfiguration
            ->expects($this->once())
            ->method('getPageParameter')
            ->willReturn('page')
        ;
        $admin
            ->expects($this->once())
            ->method('getEntityClass')
            ->willReturn('MyClass')
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getMaxPerPage')
            ->willReturn(25)
        ;

        $data = new ArrayCollection([new stdClass()]);
        $dataProvider
            ->expects($this->once())
            ->method('getCollection')
            ->with('MyClass', [], [], 1, 25)
            ->willReturn($data)
        ;

        $event
            ->expects($this->once())
            ->method('setData')
            ->with($data)
        ;

        $listener->__invoke($event);
    }

    /**
     * @return LoadDataListener[]|MockObject[]
     */
    private function createListener(): array
    {
        $registry = $this->createMock(DataProviderRegistryInterface::class);
        $listener = new LoadDataListener($registry);

        return [
            $listener,
            $registry,
        ];
    }
}
