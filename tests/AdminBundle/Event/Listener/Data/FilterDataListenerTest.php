<?php

namespace LAG\AdminBundle\Tests\Event\Listener\Data;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Event\Listener\Data\FilterDataListener;
use LAG\AdminBundle\Factory\Form\FilterFormFactoryInterface;
use LAG\AdminBundle\Filter\FilterInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FilterDataListenerTest extends TestCase
{
    public function testInvoke(): void
    {
        [$listener, $filterFormFactory,,] = $this->createListener();

        $request = new Request();

        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->once())
            ->method('getFilters')
            ->willReturn([
                'my_filter' => [
                    'name' => 'my_filter',
                    'comparator' => 'like',
                    'operator' => '=',
                ],
                'empty_filter' => [],
                'false_filter' => [],
            ])
        ;

        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;

        $dataEvent = $this->createMock(DataEvent::class);
        $dataEvent
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;
        $dataEvent
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;

        $filterForm = $this->createMock(FormInterface::class);

        $filterFormFactory
            ->expects($this->once())
            ->method('create')
            ->with($admin)
            ->willReturn($filterForm)
        ;

        $filterForm
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $filterForm
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;
        $filterForm
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;

        $filterForm
            ->expects($this->once())
            ->method('getData')
            ->willReturn([
                'my_filter' => 'my_name_value',
                'false_filter' => false,
            ])
        ;

        $dataEvent
            ->expects($this->once())
            ->method('addFilter')
            ->willReturnCallback(function ($value) use ($dataEvent) {
                $this->assertInstanceOf(FilterInterface::class, $value);

                return $dataEvent;
            })
        ;
        $dataEvent
            ->expects($this->once())
            ->method('setFilterForm')
            ->with($filterForm)
        ;

        $listener->__invoke($dataEvent);
    }

    /**
     * @return FilterDataListener[]|MockObject[]
     */
    private function createListener(): array
    {
        $filterFormFactory = $this->createMock(FilterFormFactoryInterface::class);

        $listener = new FilterDataListener($filterFormFactory);

        return [
            $listener,
            $filterFormFactory,
        ];
    }
}
