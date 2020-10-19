<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\DataProvider;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\DataProvider\ORMDataProvider;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Results\ResultsHandlerInterface;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Event\ORMFilterEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Fixtures\FakeEntity;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ORMDataProviderTest extends AdminTestBase
{
    /**
     * @dataProvider getCollectionProvider
     */
    public function testGetCollection($method)
    {
        list($provider, $entityManager, $eventDispatcher, $handler,) = $this->createProvider();

        // Create an admin configuration to test the collection method
        $action = $this->createActionWithConfigurationMock([
            ['repository_method', $method],
            ['pager', null],
            ['page_parameter', 'page'],
            ['max_per_page', 666],
        ]);

        $admin = $this->createAdminWithConfigurationMock([
            ['entity', 'MyClass'],
            ['pager', null],
            ['page_parameter', 'page'],
            ['max_per_page', 666],
        ], new Request([
            'page' => 5,
        ]));
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;

        // The repository should return a query builder
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder)
        ;

        // The entity manager should return a repository
        $entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with('MyClass')
            ->willReturn($repository)
        ;

        // The event dispatcher should dispatched once a ORM_FILTER event
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function($event, $eventName) use ($queryBuilder, $admin) {
                $this->assertEquals(Events::DOCTRINE_ORM_FILTER, $eventName);
                /** @var ORMFilterEvent $event */
                $this->assertInstanceOf(ORMFilterEvent::class, $event);
                $this->assertEquals($queryBuilder, $event->getData());
                $this->assertEquals($admin, $event->getAdmin());
                $this->assertEquals([], $event->getFilters());

                return $event;
            })
        ;

        // The result handler should called to return results according to the data and parameters
        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($queryBuilder, false, 5, 666)
        ;
        

        $provider->getCollection($admin);
    }

    public function getCollectionProvider(): array
    {
        return [
            ['createQueryBuilder'],
            [null],
        ];
    }

    public function testGet()
    {
        list($provider, $entityManager,) = $this->createProvider();

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->atLeastOnce())
            ->method('find')
            ->with(42)
            ->willReturn(new FakeEntity(42))
        ;
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('getRepository')
            ->with('MyClass')
            ->willReturn($repository)
        ;

        $admin = $this->createAdminWithConfigurationMock([
            ['entity', 'MyClass'],
            ['pager', null],
            ['page_parameter', 'page'],
        ]);

        $item = $provider->get($admin, 42);

        $this->assertEquals(42, $item->getId());
    }

    public function testGetItemWithException()
    {
        list($provider, $entityManager,) = $this->createProvider();

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->atLeastOnce())
            ->method('find')
            ->with(42)
            ->willReturn(null)
        ;
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('getRepository')
            ->with('MyClass')
            ->willReturn($repository)
        ;

        $admin = $this->createAdminWithConfigurationMock([
            ['entity', 'MyClass'],
            ['pager', null],
            ['page_parameter', 'page'],
        ]);
        $this->expectException(Exception::class);

            $provider->get($admin, 42);
    }

    public function testSaveItem()
    {
        list($provider, $entityManager,) = $this->createProvider();

        $entity = new FakeEntity(42);
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('persist')
            ->with($entity)
        ;
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('flush')
        ;
        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getEntities')
            ->willReturn(new ArrayCollection([
                $entity,
            ]))
        ;

        $provider->save($admin);
    }

    public function testCreate()
    {
        list($provider,) = $this->createProvider();

        $admin = $this->createAdminWithConfigurationMock([
            ['entity', FakeEntity::class],
        ]);

        $entity = $provider->create($admin);

        $this->assertInstanceOf(FakeEntity::class, $entity);
    }

    public function testDelete()
    {
        list($provider, $entityManager) = $this->createProvider();

        $entity = new FakeEntity();
        $entities = new ArrayCollection([
            $entity,
        ]);

        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getEntities')
            ->willReturn($entities)
        ;

        $entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($entity)
        ;
        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $provider->delete($admin);
    }

    public function testDeleteWithoutEntities()
    {
        list($provider,) = $this->createProvider();

        $entities = new ArrayCollection();

        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getEntities')
            ->willReturn($entities)
        ;
        $this->expectException(Exception::class);
        $provider->delete($admin);
    }

    /**
     * @return MockObject[]|ORMDataProvider[]
     */
    protected function createProvider(): array
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $handler = $this->createMock(ResultsHandlerInterface::class);

        $provider = new ORMDataProvider(
            $entityManager,
            $eventDispatcher,
            $handler
        );

        return [
            $provider,
            $entityManager,
            $eventDispatcher,
            $handler,
        ];
    }
}
