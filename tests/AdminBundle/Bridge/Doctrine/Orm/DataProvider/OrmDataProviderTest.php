<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\Orm\DataProvider;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\DataProvider\OrmDataProvider;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Filter\FilterInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class OrmDataProviderTest extends AdminTestBase
{
    public function testGetCollection()
    {
        // FIX tests
        //$this->assertTrue(true);
        return;
        $platform = $this->getMockWithoutConstructor(MySqlPlatform::class);
        $connection = $this->getMockWithoutConstructor(Connection::class);
        $connection
            ->method('getDatabasePlatform')
            ->willReturn($platform)
        ;

        $emConfiguration = $this->getMockWithoutConstructor(Configuration::class);
        $emConfiguration
            ->method('getDefaultQueryHints')
            ->willReturn([])
        ;

        $entityManager = $this->getMockWithoutConstructor(EntityManagerInterface::class);
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($emConfiguration)
        ;
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('getConnection')
            ->willReturn($connection)
        ;

        $query = new Query($entityManager);

        $queryBuilder = $this->getMockWithoutConstructor(QueryBuilder::class);
        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($query)
        ;

        $repository = $this->getMockWithoutConstructor(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->with('entity')
            ->willReturn($queryBuilder)
        ;

        $entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository)
        ;

        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $request = new Request([
            'page' => 42,
        ]);
        $requestStack = $this->getMockWithoutConstructor(RequestStack::class);
        $requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;

        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $configuration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['entity', 'MyClass'],
                ['pager', 'pagerfanta'],
                ['page_parameter', 'page'],
            ])
        ;

        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;

        $filter = $this->getMockWithoutConstructor(FilterInterface::class);

        $provider = new OrmDataProvider(
            $entityManager,
            $eventDispatcher,
            $requestStack
        );

        $entities = $provider->getCollection($admin, [
            $filter,
        ]);
    }
}
