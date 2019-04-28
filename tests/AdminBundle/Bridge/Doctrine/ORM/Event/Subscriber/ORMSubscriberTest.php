<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Event\Subscriber;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Event\ORMFilterEvent;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Event\Subscriber\ORMSubscriber;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Filter\FilterInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;
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
        list($subscriber, $requestStack) = $this->createSubscriber();

        $request = new Request();
        $requestStack
            ->expects($this->atLeastOnce())
            ->method('getMasterRequest')
            ->willReturn($request)
        ;
        $admin = $this->createMock(AdminInterface::class);
        $action = $this->createMock(ActionInterface::class);

        $configuration = $this->createMock(ActionConfiguration::class);
        $configuration
            ->expects($this->once())
            ->method('getParameter')
            ->with('order')
            ->willReturn([
                'id' => 'desc',
            ])
        ;

        $queryBuilder = $this->createMock(QueryBuilder::class);
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

        $event = $this->createMock(ORMFilterEvent::class);
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

        $subscriber->addOrder($event);
    }

    public function testAddOrderWithSort()
    {
        list($subscriber, $requestStack, ) = $this->createSubscriber();

        $request = new Request([
            'sort' => 'name',
        ]);
        $requestStack
            ->expects($this->atLeastOnce())
            ->method('getMasterRequest')
            ->willReturn($request)
        ;
        $admin = $this->createMock(AdminInterface::class);
        $action = $this->createMock(ActionInterface::class);

        $configuration = $this->createMock(ActionConfiguration::class);

        $queryBuilder = $this->createMock(QueryBuilder::class);
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

        $event = $this->createMock(ORMFilterEvent::class);
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

        $subscriber->addOrder($event);
    }

    public function testAddFilters()
    {
        list($subscriber) = $this->createSubscriber();

        $queryBuilder = $this->createMock(QueryBuilder::class);
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

        $filter1 = $this->createMock(FilterInterface::class);
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

        $event = $this->createMock(ORMFilterEvent::class);
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

        $subscriber->addFilters($event);
    }

    /**
     * @return ORMSubscriber[]|MockObject[]
     */
    private function createSubscriber(): array
    {
        $requestStack = $this->createMock(RequestStack::class);
        $helper = $this->createMock(MetadataHelperInterface::class);

        $subscriber = new ORMSubscriber($requestStack, $helper);

        return [
            $subscriber,
            $requestStack,
            $helper,
        ];
    }
}
