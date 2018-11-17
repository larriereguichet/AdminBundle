<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Event\Subscriber;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Event\ORMFilterEvent;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Event\Subscriber\ORMSubscriber;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Filter\FilterInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ORMSubscriberTest extends AdminTestBase
{
    public function testGetSubscribedEvents()
    {
        $events = ORMSubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(Events::DOCTRINE_ORM_FILTER, $events);
        $this->assertContains('addOrder', $events[Events::DOCTRINE_ORM_FILTER][0]);
        $this->assertContains('addFilters', $events[Events::DOCTRINE_ORM_FILTER][1]);
    }

    public function testAddOrder()
    {
        $request = new Request();

        $requestStack = $this->getMockWithoutConstructor(RequestStack::class);
        $requestStack
            ->expects($this->atLeastOnce())
            ->method('getMasterRequest')
            ->willReturn($request)
        ;
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $action = $this->getMockWithoutConstructor(ActionInterface::class);

        $configuration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $configuration
            ->expects($this->once())
            ->method('getParameter')
            ->with('order')
            ->willReturn([
                'id' => 'desc',
            ])
        ;

        $queryBuilder = $this->getMockWithoutConstructor(QueryBuilder::class);
        $queryBuilder
            ->expects($this->once())
            ->method('getRootAliases')
            ->willReturn([
                'entity',
            ])
        ;
        $queryBuilder
            ->expects($this->once())
            ->method('addOrderBy')
            ->with('entity.id', 'desc')
        ;

        $event = $this->getMockWithoutConstructor(ORMFilterEvent::class);
        $event
            ->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilder)
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
            ->willReturn($configuration)
        ;

        $subscriber = new ORMSubscriber($requestStack);
        $subscriber->addOrder($event);
    }

    public function testAddOrderWithSort()
    {
        $request = new Request([
            'sort' => 'name',
        ]);

        $requestStack = $this->getMockWithoutConstructor(RequestStack::class);
        $requestStack
            ->expects($this->atLeastOnce())
            ->method('getMasterRequest')
            ->willReturn($request)
        ;
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $action = $this->getMockWithoutConstructor(ActionInterface::class);

        $configuration = $this->getMockWithoutConstructor(ActionConfiguration::class);

        $queryBuilder = $this->getMockWithoutConstructor(QueryBuilder::class);
        $queryBuilder
            ->expects($this->once())
            ->method('getRootAliases')
            ->willReturn([
                'entity',
            ])
        ;
        $queryBuilder
            ->expects($this->once())
            ->method('addOrderBy')
            ->with('entity.name', 'asc')
        ;

        $event = $this->getMockWithoutConstructor(ORMFilterEvent::class);
        $event
            ->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilder)
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
            ->willReturn($configuration)
        ;

        $subscriber = new ORMSubscriber($requestStack);
        $subscriber->addOrder($event);
    }

    public function testAddFilters()
    {
        $requestStack = $this->getMockWithoutConstructor(RequestStack::class);
        $queryBuilder = $this->getMockWithoutConstructor(QueryBuilder::class);
        $queryBuilder
            ->expects($this->once())
            ->method('getRootAliases')
            ->willReturn([
                'entity',
            ])
        ;
        $queryBuilder
            ->expects($this->once())
            ->method('orWhere')
            ->with('entity.name like :filter_name')
        ;
        $queryBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('filter_name', '%test%')
        ;

        $filter1 = $this->getMockWithoutConstructor(FilterInterface::class);
        $filter1
            ->method('getName')
            ->willReturn('name')
        ;
        $filter1
            ->method('getOperator')
            ->willReturn('like')
        ;
        $filter1
            ->expects($this->once())
            ->method('getValue')
            ->willReturn('test')
        ;
        $filter1
            ->expects($this->atLeastOnce())
            ->method('getComparator')
            ->willReturn('like')
        ;

        $event = $this->getMockWithoutConstructor(ORMFilterEvent::class);
        $event
            ->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilder)
        ;
        $event
            ->expects($this->once())
            ->method('getFilters')
            ->willReturn([
                $filter1,
            ])
        ;

        $subscriber = new ORMSubscriber($requestStack);
        $subscriber->addFilters($event);
    }
}
