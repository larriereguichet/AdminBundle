<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\State;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Exception\ManagerNotFoundException;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\Tests\Entity\FakeEntity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ORMDataProcessorTest extends TestCase
{
    private ORMProcessor $processor;
    private MockObject $registry;

    #[Test]
    #[DataProvider('persistOperations')]
    public function itPersistsData(OperationInterface $operation): void
    {
        $data = new FakeEntity();
        $manager = $this->createMock(EntityManagerInterface::class);

        $this->registry
            ->expects($this->once())
            ->method('getManagerForClass')
            ->with(FakeEntity::class)
            ->willReturn($manager)
        ;
        $manager->expects($this->once())
            ->method('persist')
            ->with($data)
        ;
        $manager->expects($this->once())
            ->method('flush')
        ;

        $this->processor->process($data, $operation);
    }

    #[Test]
    public function itDeletesData(): void
    {
        $data = new FakeEntity();
        $manager = $this->createMock(EntityManagerInterface::class);

        $this->registry
            ->expects($this->once())
            ->method('getManagerForClass')
            ->with(FakeEntity::class)
            ->willReturn($manager)
        ;
        $manager->expects($this->once())
            ->method('remove')
            ->with($data)
        ;
        $manager->expects($this->once())
            ->method('flush')
        ;

        $this->processor->process($data, (new Delete())->setResource(new Resource(resourceClass: FakeEntity::class)));
    }

    #[Test]
    public function itDoesNotSavedNotManagedData(): void
    {
        $operation = (new Delete())->setResource(new Resource(resourceClass: FakeEntity::class));
        $data = new FakeEntity();
        $this->registry
            ->expects($this->once())
            ->method('getManagerForClass')
            ->with(FakeEntity::class)
            ->willReturn(null)
        ;

        self::expectExceptionObject(new ManagerNotFoundException($operation));
        $this->processor->process($data, $operation);
    }

    public static function persistOperations(): iterable
    {
        $resource = new Resource(
            operations: [new Create(), new Update()],
            resourceClass: FakeEntity::class,
        );

        yield [$resource->getOperation('create')->setResource($resource)];
        yield [$resource->getOperation('update')->setResource($resource)];
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(Registry::class);
        $this->processor = new ORMProcessor($this->registry);
    }
}
