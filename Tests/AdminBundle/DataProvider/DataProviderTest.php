<?php

namespace BlueBear\AdminBundle\Tests\AdminBundle\DataProvider;

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
        // repository save method SHOULD be called
        $repositoryMock = $this->mockEntityRepository();
        $repositoryMock
            ->expects($this->once())
            ->method('save');

        $dataProvider = new DataProvider($repositoryMock);
        $dataProvider->save(new stdClass());
    }

    /**
     * Method delete SHOULD be called on the entity repository.
     */
    public function testDelete()
    {
        // entity manager delete method SHOULD be called
        $repositoryMock = $this->mockEntityRepository();
        $repositoryMock
            ->expects($this->once())
            ->method('delete');

        $dataProvider = new DataProvider($repositoryMock);
        $dataProvider->remove(new stdClass());
    }

    /**
     * Method find SHOULD be called on the entity repository.
     */
    public function testFind()
    {
        // repository find method SHOULD be called
        $repositoryMock = $this->mockEntityRepository();
        $repositoryMock
            ->expects($this->once())
            ->method('find');

        $dataProvider = new DataProvider($repositoryMock);
        $dataProvider->find('unique_id');
    }

    /**
     * Method findBy SHOULD be called on the entity repository.
     */
    public function testFindBy()
    {
        // repository findBy method SHOULD be called
        $repositoryMock = $this->mockEntityRepository();
        $repositoryMock
            ->expects($this->once())
            ->method('findBy');

        $dataProvider = new DataProvider($repositoryMock);
        $dataProvider->findBy([]);
    }

    /**
     * Method create SHOULD return a new instance of the given class.
     */
    public function testCreate()
    {
        // repository findBy method SHOULD be called
        $repositoryMock = $this->mockEntityRepository();
        $repositoryMock
            ->expects($this->once())
            ->method('getClassName')
            ->willReturn(self::class);
        $dataProvider = new DataProvider($repositoryMock);
        $test = $dataProvider->create();

        $this->assertEquals(get_class($test), self::class);
    }
}
