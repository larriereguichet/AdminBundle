<?php

namespace BlueBear\AdminBundle\Tests\AdminBundle\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use LAG\AdminBundle\DataProvider\DataProvider;
use LAG\DoctrineRepositoryBundle\Repository\RepositoryInterface;
use PHPUnit_Framework_TestCase;

class DataProviderTest extends PHPUnit_Framework_TestCase
{
    public function testSave()
    {
        /** @var RepositoryInterface $repositoryMock */
        $repositoryMock = $this
            ->getMockBuilder('LAG\DoctrineRepositoryBundle\Repository\RepositoryInterface')
            ->getMock();
        /** @var EntityManagerInterface $entityManagerMock */
        $entityManagerMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $dataProvider = new DataProvider($repositoryMock, $entityManagerMock);
        $dataProvider->save(new \stdClass());
    }
}
