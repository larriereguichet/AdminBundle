<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Results\ResultsHandlerInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORMDataProvider;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Filter\FilterInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\HttpFoundation\Request;

class ORMDataProviderTest extends TestCase
{
    private ORMDataProvider $dataProvider;
    private MockObject $entityManager;
    private MockObject $helper;
    private MockObject $resultsHandler;

    /**
     * @dataProvider collectionDataProvider
     */
    public function testGetCollection(
        string $class,
        int $page,
        int $limit,
        array $orderBy,
        array $filters
    ): void {
        $request = new Request([
            'page' => $page,
        ]);
        $entity = $this->createMock(stdClass::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->with('entity')
            ->willReturn($queryBuilder)
        ;

        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->once())
            ->method('getRepositoryMethod')
            ->willReturn(null)
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getPager')
            ->willReturn('pagerfanta')
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getPageParameter')
            ->willReturn('page')
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getMaxPerPage')
            ->willReturn($limit)
        ;

        $configuration = $this->createMock(AdminConfiguration::class);
        $configuration
            ->expects($this->once())
            ->method('getEntityClass')
            ->willReturn($class)
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
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($class)
            ->willReturn($repository)
        ;

        $this->helper
            ->expects($this->atLeastOnce())
            ->method('getAdmin')
            ->willReturn($admin)
        ;
        $expectException = false;

        if (count($filters) > 0) {
            foreach ($filters as $filter) {
                if (!$filter instanceof FilterInterface) {
                    $this->expectException(Exception::class);
                    $expectException = true;
                } else {
                    if ($filter instanceof MockObject) {
                        $queryBuilder
                            ->expects($this->exactly(2))
                            ->method('getRootAliases')
                            ->willReturn(['entity'])
                        ;
                    }
                }
            }
        }

        if (!$expectException) {
            $this->resultsHandler
                ->expects($this->once())
                ->method('handle')
                ->with($queryBuilder, true, $page, $limit)
                ->willReturn(new ArrayCollection([$entity]))
            ;
        }

        $data = $this->dataProvider->getCollection($class, $filters, $orderBy, $page, $limit);

        $this->assertEquals(new ArrayCollection([$entity]), $data);
    }

    /**
     * @dataProvider collectionDataProvider
     */
    public function testGetCollectionWithWrongRepository(
        string $class,
        int $page,
        int $limit,
        array $orderBy,
        array $filters
    ): void {
        $repository = $this->createMock(stdClass::class);
        $actionConfiguration = $this->createMock(ActionConfiguration::class);

        $configuration = $this->createMock(AdminConfiguration::class);
        $configuration
            ->expects($this->once())
            ->method('getEntityClass')
            ->willReturn($class)
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
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($class)
            ->willReturn($repository)
        ;

        $this->helper
            ->expects($this->atLeastOnce())
            ->method('getAdmin')
            ->willReturn($admin)
        ;

        $this->resultsHandler
            ->expects($this->never())
            ->method('handle')
        ;

        if (count($filters) > 0) {
            foreach ($filters as $filter) {
                if (!$filter instanceof FilterInterface) {
                    $this->expectException(Exception::class);
                }
            }
        }

        $this->expectException(Exception::class);
        $this->dataProvider->getCollection($class, $filters, $orderBy, $page, $limit);
    }

    /**
     * @dataProvider collectionDataProvider
     */
    public function testGetCollectionWithCustomRepositoryMethod(
        string $class,
        int $page,
        int $limit,
        array $orderBy,
        array $filters
    ): void {
        $request = new Request([
            'page' => $page,
        ]);
        $entity = $this->createMock(stdClass::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with($filters, $orderBy, $page, $limit)
            ->willReturn([$entity])
        ;

        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->once())
            ->method('getRepositoryMethod')
            ->willReturn('findBy')
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getPager')
            ->willReturn('pagerfanta')
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getPageParameter')
            ->willReturn('page')
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getMaxPerPage')
            ->willReturn($limit)
        ;

        $configuration = $this->createMock(AdminConfiguration::class);
        $configuration
            ->expects($this->once())
            ->method('getEntityClass')
            ->willReturn($class)
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
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($class)
            ->willReturn($repository)
        ;

        $this->helper
            ->expects($this->atLeastOnce())
            ->method('getAdmin')
            ->willReturn($admin)
        ;

        $results = new ArrayCollection([$entity]);
        $this->resultsHandler
            ->expects($this->once())
            ->method('handle')
            ->with([$entity], true, $page, $limit)
            ->willReturn($results)
        ;

        $data = $this->dataProvider->getCollection($class, $filters, $orderBy, $page, $limit);

        $this->assertEquals($results, $data);
    }

    /**
     * @dataProvider collectionDataProvider
     */
    public function testGetCollectionWithWrongCustomRepositoryMethod(
        string $class,
        int $page,
        int $limit,
        array $orderBy,
        array $filters
    ): void {
        $request = new Request();
        $repository = $this->createMock(EntityRepository::class);

        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->once())
            ->method('getRepositoryMethod')
            ->willReturn('wrong')
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getPager')
            ->willReturn('pagerfanta')
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getPageParameter')
            ->willReturn('page')
        ;
        $actionConfiguration
            ->expects($this->once())
            ->method('getMaxPerPage')
            ->willReturn($limit)
        ;

        $configuration = $this->createMock(AdminConfiguration::class);
        $configuration
            ->expects($this->once())
            ->method('getEntityClass')
            ->willReturn($class)
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
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($class)
            ->willReturn($repository)
        ;

        $this->helper
            ->expects($this->atLeastOnce())
            ->method('getAdmin')
            ->willReturn($admin)
        ;

        $this->resultsHandler
            ->expects($this->never())
            ->method('handle')
        ;

        if (count($filters) > 0) {
            foreach ($filters as $filter) {
                if (!$filter instanceof FilterInterface) {
                    $this->expectException(Exception::class);
                }
            }
        }
        $this->expectException(Exception::class);
        $this->dataProvider->getCollection($class, $filters, $orderBy, $page, $limit);
    }

    public function testCreate(): void
    {
        $entity = $this->dataProvider->create(stdClass::class);
        $this->assertInstanceOf(stdClass::class, $entity);
    }

    public function collectionDataProvider(): array
    {
        $filter = $this->createMock(FilterInterface::class);

        return [
            ['MyLittleClass', 1, 25, [], []],
            ['MyLittleClass', 5, 999, ['title' => 'desc'], ['wrong']],
            ['MyLittleClass', 5, 999, ['title' => 'desc'], [$filter]],
        ];
    }

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->helper = $this->createMock(AdminHelperInterface::class);
        $this->resultsHandler = $this->createMock(ResultsHandlerInterface::class);
        $this->dataProvider = new ORMDataProvider($this->entityManager, $this->helper, $this->resultsHandler);
    }
}
