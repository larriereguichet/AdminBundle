<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Helper\AdminContextInterface;
use LAG\AdminBundle\Bridge\Doctrine\DataSource\ORMDataSource;
use LAG\AdminBundle\Bridge\Doctrine\ORMDataProvider;
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
        $data = $this->dataProvider->getCollection($class, $filters, $orderBy, $page, $limit);

        $this->assertInstanceOf(ORMDataSource::class, $data);
        $this->assertInstanceOf(QueryBuilder::class, $data->getData());
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

        $queryBuilder = $this->createMock(QueryBuilder::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with($filters, $orderBy, $page, $limit)
            ->willReturn($queryBuilder)
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

        $data = $this->dataProvider->getCollection($class, $filters, $orderBy, $page, $limit);

        $this->assertEquals($queryBuilder, $data->getData());
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

    public function testGet(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $data = new stdClass();

        $this
            ->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with('MyClass')
            ->willReturn($repository)
        ;
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(666)
            ->willReturn($data)
        ;
        $foundData = $this->dataProvider->get('MyClass', 666);
        $this->assertEquals($data, $foundData);
    }

    public function testGetReturnNull(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $this
            ->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with('MyClass')
            ->willReturn($repository)
        ;
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(666)
            ->willReturn(null)
        ;
        $this->expectException(Exception::class);
        $this->dataProvider->get('MyClass', 666);
    }

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->helper = $this->createMock(AdminContextInterface::class);
        $this->dataProvider = new ORMDataProvider($this->entityManager, $this->helper);
    }
}
