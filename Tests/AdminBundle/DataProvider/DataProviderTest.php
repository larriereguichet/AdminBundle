<?php

namespace BlueBear\AdminBundle\Tests\AdminBundle\DataProvider;

use LAG\AdminBundle\DataProvider\DataProvider;
use LAG\AdminBundle\Tests\Base;
use stdClass;

/**
 * Test the built-in data provider
 */
class DataProviderTest extends Base
{
    /**
     * Method save SHOULD be called on the entiy repository
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
     * Method delete SHOULD be called on the entity repository
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
     * Method find SHOULD be called on the entity repository
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
     * Method findBy SHOULD be called on the entity repository
     */
    public function testfindBy()
    {
        // repository findBy method SHOULD be called
        $repositoryMock = $this->mockEntityRepository();
        $repositoryMock
            ->expects($this->once())
            ->method('findBy');

        $dataProvider = new DataProvider($repositoryMock);
        $dataProvider->findBy([]);
    }
}
