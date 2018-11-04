<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\DataProvider;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\DataProvider\ORMDataProvider;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\DoctrineOrmFilterEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Fixtures\EntityFixture;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ORMDataProviderTest extends AdminTestBase
{
    public function testGetCollection()
    {
        // Create an admin configuration to test the collection method
        $configuration = $this->createMock(AdminConfiguration::class);
        $configuration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['entity', 'MyClass'],
                ['pager', null],
                ['page_parameter', 'page'],
            ])
        ;

        /** @var AdminInterface|MockObject $admin */
        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;

        // Create fake entities to return
        $entities = [
            new EntityFixture(uniqid()),
            new EntityFixture(uniqid()),
        ];

        // The query should return entities
        $query = $this->createMock(AbstractQuery::class);
        $query
            ->expects($this->once())
            ->method('getResult')
            ->willReturn($entities)
        ;

        // The query builder should call the getQuery() method
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($query)
        ;

        // The repository should return a query builder
        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->with('entity')
            ->willReturn($queryBuilder)
        ;

        // The entity manager should return a repository
        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository)
        ;

        // The event dispatcher should dispatched once a ORM_FILTER event
        /** @var EventDispatcherInterface|MockObject $eventDispatcher */
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($eventName, $event) use ($queryBuilder, $admin) {
                $this->assertEquals(AdminEvents::DOCTRINE_ORM_FILTER, $eventName);
                /** @var DoctrineOrmFilterEvent $event */
                $this->assertInstanceOf(DoctrineOrmFilterEvent::class, $event);
                $this->assertEquals($queryBuilder, $event->getQueryBuilder());
                $this->assertEquals($admin, $event->getAdmin());
                $this->assertEquals([], $event->getFilters());
            })
        ;
        /** @var RequestStack|MockObject $requestStack */
        $requestStack = $this->createMock(RequestStack::class);

        $provider = new ORMDataProvider(
            $entityManager,
            $eventDispatcher,
            $requestStack
        );
        $returnedEntities = $provider->getCollection($admin);

        // The returned entities should be the same as those provided by the repository
        $this->assertEquals($entities, $returnedEntities);
    }

    public function testGetItem()
    {
        /** @var EventDispatcherInterface|MockObject $eventDispatcher */
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        /** @var RequestStack|MockObject $requestStack */
        $requestStack = $this->createMock(RequestStack::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->atLeastOnce())
            ->method('find')
            ->with(42)
            ->willReturn(new EntityFixture(42))
        ;

        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('getRepository')
            ->with('MyClass')
            ->willReturn($repository)
        ;

        $configuration = $this->createMock(AdminConfiguration::class);
        $configuration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['entity', 'MyClass'],
                ['pager', null],
                ['page_parameter', 'page'],
            ])
        ;

        /** @var AdminInterface|MockObject $admin */
        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;

        $provider = new ORMDataProvider(
            $entityManager,
            $eventDispatcher,
            $requestStack
        );

        $item = $provider->get($admin, 42);

        $this->assertEquals(42, $item->getId());
    }

    public function testGetItemWithException()
    {
        /** @var EventDispatcherInterface|MockObject $eventDispatcher */
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        /** @var RequestStack|MockObject $requestStack */
        $requestStack = $this->createMock(RequestStack::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->atLeastOnce())
            ->method('find')
            ->with(42)
            ->willReturn(null)
        ;

        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('getRepository')
            ->with('MyClass')
            ->willReturn($repository)
        ;

        $configuration = $this->createMock(AdminConfiguration::class);
        $configuration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['entity', 'MyClass'],
                ['pager', null],
                ['page_parameter', 'page'],
            ])
        ;

        /** @var AdminInterface|MockObject $admin */
        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;

        $provider = new ORMDataProvider(
            $entityManager,
            $eventDispatcher,
            $requestStack
        );

        $this->assertExceptionRaised(Exception::class, function () use ($provider, $admin) {
            $provider->get($admin, 42);
        });
    }

    public function testSaveItem()
    {
        /** @var EventDispatcherInterface|MockObject $eventDispatcher */
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        /** @var RequestStack|MockObject $requestStack */
        $requestStack = $this->createMock(RequestStack::class);

        $entity =new EntityFixture(42);

        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('persist')
            ->with($entity)
        ;
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('flush')
        ;

        /** @var AdminInterface|MockObject $admin */
        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getEntities')
            ->willReturn(new ArrayCollection([
                $entity,
            ]))
        ;

        $provider = new ORMDataProvider(
            $entityManager,
            $eventDispatcher,
            $requestStack
        );

        $provider->save($admin);
    }
}
