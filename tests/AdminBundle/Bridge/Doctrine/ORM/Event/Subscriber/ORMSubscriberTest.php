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
use LAG\AdminBundle\Event\Events\FieldEvent;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Field\Definition\FieldDefinitionInterface;
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
            ->method('getData')
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
    
    public function testAddOrderWithoutQueryBuilder()
    {
        list($subscriber,) = $this->createSubscriber();
        
        $event = $this->createMock(ORMFilterEvent::class);
        $event
            ->expects($this->once())
            ->method('getData')
            ->willReturn([
                'test',
            ])
        ;
        $event
            ->expects($this->never())
            ->method('getAdmin')
        ;
    
        $subscriber->addOrder($event);
    }

    public function testAddOrderWithSort()
    {
        list($subscriber, $requestStack,) = $this->createSubscriber();

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
            ->method('getData')
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
            ->method('getData')
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

    public function testFormType()
    {
        list($subscriber,,$helper) = $this->createSubscriber();

        $field = $this->createMock(FieldDefinitionInterface::class);
        $helper
            ->expects($this->once())
            ->method('getFields')
            ->with('MyEntity')
            ->willReturn([
                'id' => $field,
            ])
        ;

        $event = $this->createMock(FormEvent::class);
        $admin = $this->createAdminWithConfigurationMock([
            ['entity', 'MyEntity'],
        ]);

        $event
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;

        $subscriber->guessFormType($event);
    }

    public function testFormTypeWithDefinedForm()
    {
        list($subscriber) = $this->createSubscriber();

        $event = $this->createMock(FormEvent::class);
        $admin = $this->createAdminWithConfigurationMock([
            ['form', 'MyForm'],
        ]);

        $event
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;

        $subscriber->guessFormType($event);
    }

    public function testGuessType()
    {
        list($subscriber,,$helper) = $this->createSubscriber();

        $event = $this->createMock(FieldEvent::class);
        $event
            ->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(null)
        ;
        $event
            ->expects($this->atLeastOnce())
            ->method('getEntityClass')
            ->willReturn('MyEntity')
        ;
        $event
            ->expects($this->atLeastOnce())
            ->method('getFieldName')
            ->willReturn('name')
        ;
        $event
            ->expects($this->atLeastOnce())
            ->method('setType')
            ->with('MyType')
        ;
        $event
            ->expects($this->once())
            ->method('setOptions')
            ->with([
                'required' => false,
            ])
        ;

        $field = $this->createMock(FieldDefinitionInterface::class);
        $invalidField = $this->createMock(FieldDefinitionInterface::class);
        $field
            ->expects($this->once())
            ->method('getType')
            ->willReturn('MyType')
        ;
        $field
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn([
                'required' => false,
            ])
        ;
        $helper
            ->expects($this->once())
            ->method('getFields')
            ->with('MyEntity')
            ->willReturn([
                'name' => $field,
                'createdAt' => $invalidField,
            ])
        ;

        $subscriber->guessType($event);
    }

    public function testGuessTypeWithNoDefinition()
    {
        list($subscriber,,$helper) = $this->createSubscriber();

        $event = $this->createMock(FieldEvent::class);
        $event
            ->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(null)
        ;
        $event
            ->expects($this->atLeastOnce())
            ->method('getEntityClass')
            ->willReturn('MyEntity')
        ;
        $event
            ->expects($this->atLeastOnce())
            ->method('getFieldName')
            ->willReturn('createdAt')
        ;
        $field = $this->createMock(FieldDefinitionInterface::class);

        $helper
            ->expects($this->once())
            ->method('getFields')
            ->with('MyEntity')
            ->willReturn([
                'name' => $field,
            ])
        ;

        $subscriber->guessType($event);
    }

    public function testGuessTypeWithId()
    {
        list($subscriber,,$helper) = $this->createSubscriber();

        $event = $this->createMock(FieldEvent::class);
        $event
            ->expects($this->once())
            ->method('getType')
            ->willReturn(null)
        ;
        $event
            ->expects($this->once())
            ->method('getEntityClass')
            ->willReturn('MyEntity')
        ;
        $event
            ->expects($this->once())
            ->method('getFieldName')
            ->willReturn('id')
        ;

        $field = $this->createMock(FieldDefinitionInterface::class);
        $helper
            ->expects($this->once())
            ->method('getFields')
            ->with('MyEntity')
            ->willReturn([
                'id' => $field,
            ])
        ;

        $subscriber->guessType($event);
    }

    public function testGuessTypeDefinedType()
    {
        list($subscriber) = $this->createSubscriber();

        $event = $this->createMock(FieldEvent::class);
        $event
            ->expects($this->once())
            ->method('getType')
            ->willReturn('MyType')
        ;

        $subscriber->guessType($event);
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
