<?php

namespace LAG\AdminBundle\Tests\AdminBundle\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use LAG\AdminBundle\DataProvider\DataProvider;
use LAG\AdminBundle\Tests\AdminTestBase;
use stdClass;

/**
 * Test the built-in data provider
 */
class DataProviderTest extends AdminTestBase
{
    /**
     * Method save SHOULD be called on the entiy repository.
     */
    public function testSave()
    {
        $entityManager = $this->getMockWithoutConstructor(EntityManagerInterface::class);
        
        // The repository save method SHOULD be called
        $repositoryMock = $this->mockEntityRepository();
        $repositoryMock
            ->expects($this->once())
            ->method('save');

        $dataProvider = new DataProvider($repositoryMock, $entityManager);
        $dataProvider->save(new stdClass());
    }

    /**
     * Method delete SHOULD be called on the entity repository.
     */
    public function testDelete()
    {
        $entityManager = $this->getMockWithoutConstructor(EntityManagerInterface::class);
        
        // The entity manager delete method SHOULD be called
        $repositoryMock = $this->mockEntityRepository();
        $repositoryMock
            ->expects($this->once())
            ->method('delete');

        $dataProvider = new DataProvider($repositoryMock, $entityManager);
        $dataProvider->remove(new stdClass());
    }

    /**
     * Method find SHOULD be called on the entity repository.
     */
    public function testFind()
    {
        $entityManager = $this->getMockWithoutConstructor(EntityManagerInterface::class);
        
        // The repository find method SHOULD be called
        $repositoryMock = $this->mockEntityRepository();
        $repositoryMock
            ->expects($this->once())
            ->method('find');

        $dataProvider = new DataProvider($repositoryMock, $entityManager);
        $dataProvider->find('unique_id');
    }

    /**
     * Method findBy SHOULD be called on the entity repository.
     */
    public function testFindBy()
    {
        $entityManager = $this->getMockWithoutConstructor(EntityManagerInterface::class);
        
        // repository findBy method SHOULD be called
        $repositoryMock = $this->mockEntityRepository();
        $repositoryMock
            ->expects($this->once())
            ->method('findBy');

        $dataProvider = new DataProvider($repositoryMock, $entityManager);
        $dataProvider->findBy([]);
    }

    /**
     * Method create SHOULD return a new instance of the given class.
     */
    public function testCreate()
    {
        $entityManager = $this->getMockWithoutConstructor(EntityManagerInterface::class);
        
        // repository findBy method SHOULD be called
        $repositoryMock = $this->mockEntityRepository();
        $repositoryMock
            ->expects($this->once())
            ->method('getClassName')
            ->willReturn(self::class);
        $dataProvider = new DataProvider($repositoryMock, $entityManager);
        $test = $dataProvider->create();

        $this->assertEquals(get_class($test), self::class);
    }
}
