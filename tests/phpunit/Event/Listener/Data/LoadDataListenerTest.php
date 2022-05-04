<?php

namespace LAG\AdminBundle\Tests\Event\Listener\Data;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\DataProvider\DataSourceHandler\DataHandlerInterface;
use LAG\AdminBundle\DataProvider\DataSourceInterface;
use LAG\AdminBundle\DataProvider\Registry\DataProviderRegistryInterface;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Event\Listener\Data\LoadListener;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\DataProvider\AdminAwareDataProviderInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class LoadDataListenerTest extends TestCase
{
    private LoadListener $listener;
    private MockObject $registry;
    private MockObject $dataHandler;
    private MockObject $eventDispatcher;

    public function testInvokeWithNoneStrategy(): void
    {
        [$event,,,,$actionConfiguration] = $this->createDataEvent();

        $actionConfiguration
            ->expects($this->once())
            ->method('getLoadStrategy')
            ->willReturn(AdminInterface::LOAD_STRATEGY_NONE)
        ;
        $this->listener->__invoke($event);
    }

    public function testInvokeWithData(): void
    {
        [$event,,,,$actionConfiguration] = $this->createDataEvent();
        $data = new stdClass();
        $data->test = true;
        $event->setData($data);

        $actionConfiguration
            ->expects($this->once())
            ->method('getLoadStrategy')
            ->willReturn(AdminInterface::LOAD_STRATEGY_UNIQUE)
        ;
        $this->listener->__invoke($event);
    }

    public function testInvokeWithStrategyUnique(): void
    {
        [$event,$admin,,$adminConfiguration,$actionConfiguration,$request] = $this->createDataEvent();
        $request->query->set('id', 666);

        $dataProvider = $this->createMock(AdminAwareDataProviderInterface::class);
        $data = new stdClass();
        $data->test = true;

        $actionConfiguration
            ->expects($this->once())
            ->method('getLoadStrategy')
            ->willReturn(AdminInterface::LOAD_STRATEGY_UNIQUE)
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getRouteParameters')
            ->willReturn(['id' => []])
        ;
        $adminConfiguration
            ->expects($this->once())
            ->method('getDataProvider')
            ->willReturn('my_provider_key')
        ;
        $this
            ->registry
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
        $dataProvider
            ->expects($this->once())
            ->method('get')
            ->with('MyClass', 666)
            ->willReturn($data)
        ;
        $admin
            ->expects($this->once())
            ->method('getEntityClass')
            ->willReturn('MyClass')
        ;

        $this->listener->__invoke($event);

        $this->assertEquals($data, $event->getData());
    }

    public function testInvokeWithStrategyUniqueWithoutIdentifier(): void
    {
        [$event,$admin,$action,$adminConfiguration,$actionConfiguration,] = $this->createDataEvent();

        $dataProvider = $this->createMock(AdminAwareDataProviderInterface::class);
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
        $this
            ->registry
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
        $this->listener->__invoke($event);
    }

    public function testInvokeWithStrategyMultiple(): void
    {
        [$event,$admin,$action,$adminConfiguration,$actionConfiguration,] = $this->createDataEvent();

        $dataProvider = $this->createMock(AdminAwareDataProviderInterface::class);

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
        $this
            ->registry
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

        $dataSource = $this->createMock(DataSourceInterface::class);
        $dataProvider
            ->expects($this->once())
            ->method('getCollection')
            ->with('MyClass', [], [], 1, 25)
            ->willReturn($dataSource)
        ;
        $data = new stdClass();
        $this
            ->dataHandler
            ->expects($this->once())
            ->method('handle')
            ->willReturn($data)
        ;

        $this->listener->__invoke($event);
        $this->assertEquals($data, $event->getData());
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(DataProviderRegistryInterface::class);
        $this->dataHandler = $this->createMock(DataHandlerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->listener = new LoadListener($this->registry, $this->dataHandler, $this->eventDispatcher);
    }

    /**
     * @return DataEvent[]|Request[]|MockObject[]
     */
    private function createDataEvent(): array
    {
        $request = new Request();
        $action = $this->createMock(ActionInterface::class);
        $admin = $this->createMock(AdminInterface::class);
        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $event = new DataEvent($admin, $request);

        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
        $action
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        return [
            $event,
            $admin,
            $action,
            $adminConfiguration,
            $actionConfiguration,
            $request,
        ];
    }
}
