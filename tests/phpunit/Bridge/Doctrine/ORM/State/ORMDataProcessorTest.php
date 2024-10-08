<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\State;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Exception\ManagerNotFoundException;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Update;
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
    #[DataProvider(methodName: 'persistOperations')]
    public function itPersistsData(OperationInterface $operation): void
    {
        $data = new FakeEntity();
        $manager = self::createMock(EntityManagerInterface::class);

        $this->registry
            ->expects(self::once())
            ->method('getManagerForClass')
            ->with(FakeEntity::class)
            ->willReturn($manager)
        ;
        $manager->expects(self::once())
            ->method('persist')
            ->with($data)
        ;
        $manager->expects(self::once())
            ->method('flush')
        ;

        $this->processor->process($data, $operation);
    }

    #[Test]
    public function itDeletesData(): void
    {
        $data = new FakeEntity();
        $manager = self::createMock(EntityManagerInterface::class);

        $this->registry
            ->expects(self::once())
            ->method('getManagerForClass')
            ->with(FakeEntity::class)
            ->willReturn($manager)
        ;
        $manager->expects(self::once())
            ->method('remove')
            ->with($data)
        ;
        $manager->expects(self::once())
            ->method('flush')
        ;

        $this->processor->process($data, (new Delete())->withResource(new Resource(dataClass: FakeEntity::class)));
    }

    #[Test]
    public function itDoesNotSavedNotManagedData(): void
    {
        $operation = (new Delete())->withResource(new Resource(dataClass: FakeEntity::class));
        $data = new FakeEntity();
        $this->registry
            ->expects(self::once())
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
            dataClass: FakeEntity::class,
        );

        yield [$resource->getOperation('create')->withResource($resource)];
        yield [$resource->getOperation('update')->withResource($resource)];
    }

    protected function setUp(): void
    {
        $this->registry = self::createMock(Registry::class);
        $this->processor = new ORMProcessor($this->registry);
    }
}
