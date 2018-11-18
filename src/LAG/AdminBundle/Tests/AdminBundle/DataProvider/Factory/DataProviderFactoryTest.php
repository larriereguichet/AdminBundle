<?php

namespace LAG\AdminBundle\Tests\AdminBundle\DataProvider\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use LAG\AdminBundle\DataProvider\DataProvider;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\DataProvider\Factory\DataProviderFactory;
use LAG\AdminBundle\Doctrine\Repository\DoctrineRepository;
use LAG\AdminBundle\Tests\AdminTestBase;

class DataProviderFactoryTest extends AdminTestBase
{
    /**
     * Add method should add the given data provider to the collection with the given id.
     */
    public function testAdd()
    {
        $entityManager = $this->createMock(EntityManager::class);
        $dataProvider = $this->createMock(DataProviderInterface::class);

        $factory = new DataProviderFactory($entityManager);
        $factory->add('a_data_provider', $dataProvider);

        $this->assertTrue($factory->has('a_data_provider'));
    
        $this->assertEquals($dataProvider, $this->getPrivateProperty($factory, 'dataProviders')['a_data_provider']);
        
    }

    /**
     * Add method should throw an exception if we try to add a data provider twice.
     */
    public function testAddException()
    {
        $entityManager = $this->createMock(EntityManager::class);
        $dataProvider = $this->createMock(DataProvider::class);

        $factory = new DataProviderFactory($entityManager);
        $factory->add('a_data_provider', $dataProvider);

        $this->assertExceptionRaised(Exception::class, function () use ($factory, $dataProvider) {
            $factory->add('a_data_provider', $dataProvider);
        });
    }

    /**
     * Has method should return true if a data provider with the given id exists.
     */
    public function testHas()
    {
        $entityManager = $this->createMock(EntityManager::class);
        $dataProvider = $this->createMock(DataProvider::class);

        $factory = new DataProviderFactory($entityManager);
        $factory->add('a_data_provider', $dataProvider);

        $this->assertTrue($factory->has('a_data_provider'));
        $this->assertFalse($factory->has('no_data_provider_here'));
    }

    /**
     * Create method should return a generic data provider.
     */
    public function testCreate()
    {
        $repository = $this->createMock(DoctrineRepository::class);
        $entityManager = $this->createMock(EntityManager::class);
        $entityManager
            ->method('getRepository')
            ->with('MyEntityClass')
            ->willReturn($repository);

        $factory = new DataProviderFactory($entityManager);
        $dataProvider = $factory->create('MyEntityClass');

        $this->assertInstanceOf(DataProvider::class, $dataProvider);
    }

    /**
     * Create method should return an exception if the found repository does not implement the RepositoryInterface.
     */
    public function testCreateException()
    {
        $repository = $this->createMock(EntityRepository::class);
        $entityManager = $this->createMock(EntityManager::class);
        $entityManager
            ->method('getRepository')
            ->with('MyEntityClass')
            ->willReturn($repository);

        $factory = new DataProviderFactory($entityManager);

        $this->assertExceptionRaised(Exception::class, function () use ($factory) {
            $factory->create('MyEntityClass');
        });
    }

    public function testGet()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $dataProvider = $this->createMock(DataProvider::class);

        $factory = new DataProviderFactory($entityManager);
        $factory->add('a_data_provider', $dataProvider);

        $this->assertEquals($dataProvider, $factory->get('a_data_provider'));
        $this->assertExceptionRaised(Exception::class, function () use ($factory) {
            $factory->get('wrong_data_provider');
        });
        $this->assertExceptionRaised(Exception::class, function () use ($factory) {
            $factory->get(null, null);
        });
    }
}
