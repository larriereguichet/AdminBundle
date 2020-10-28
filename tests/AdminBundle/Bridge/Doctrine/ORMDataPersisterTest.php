<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Results\ResultsHandlerInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORMDataProvider;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Filter\FilterInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

class ORMDataPersisterTest extends TestCase
{
    public function testCreate(): void
    {
        [$dataPersister] = $this->createDataPersister();

        $entity = $dataPersister->create(stdClass::class);
        $this->assertInstanceOf(stdClass::class, $entity);
    }

    public function testGet(): void
    {
        [$dataPersister, $entityManager,] = $this->createDataPersister();

        $object = new stdClass();
        $object->test = true;

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(666)
            ->willReturn($object)
        ;

        $entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(stdClass::class)
            ->willReturn($repository)
        ;

        $entity = $dataPersister->get(stdClass::class, 666);
        $this->assertInstanceOf(stdClass::class, $entity);
        $this->assertEquals($object, $entity);
    }

    public function testGetWithNotFoundEntity(): void
    {
        [$dataPersister, $entityManager,] = $this->createDataPersister();

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(666)
            ->willReturn(null)
        ;

        $entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(stdClass::class)
            ->willReturn($repository)
        ;

        $this->expectException(Exception::class);
        $dataPersister->get(stdClass::class, 666);
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

    /**
     * @return ORMDataProvider[]|MockObject[]
     */
    private function createDataPersister(): array
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $helper = $this->createMock(AdminHelperInterface::class);
        $resultsHandler = $this->createMock(ResultsHandlerInterface::class);
        $dataPersister = new ORMDataProvider($entityManager, $helper, $resultsHandler);

        return [
            $dataPersister,
            $entityManager,
            $helper,
            $resultsHandler,
        ];
    }
}
