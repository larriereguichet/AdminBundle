<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\Factory;

use LAG\AdminBundle\Exception\InvalidGridException;
use LAG\AdminBundle\Grid\Factory\GridFactory;
use LAG\AdminBundle\Grid\Initializer\GridInitializerInterface;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Factory\GridMetadataFactoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GridFactoryTest extends TestCase
{
    private GridFactory $factory;
    private MockObject $metadataFactory;
    private MockObject $gridInitializer;
    private MockObject $validator;

    #[Test]
    public function itCreatesAGrid(): void
    {
        $resource = new Resource();
        $operation = new Index()->setResource($resource);
        $metadata = new Grid();

        $this->metadataFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->with('some_grid')
            ->willReturn($metadata)
        ;
        $this->gridInitializer
            ->expects($this->once())
            ->method('initializeGrid')
            ->with($resource, $operation, $metadata)
            ->willReturn($metadata)
        ;
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($metadata)
            ->willReturn($this->createMock(ConstraintViolationListInterface::class))
        ;

        $grid = $this->factory->create('some_grid', $operation);

        self::assertEquals($metadata, $grid);
    }

    #[Test]
    public function itDoesNotCreateInvalidGrid(): void
    {
        $resource = new Resource();
        $operation = new Index()->setResource($resource);
        $metadata = new Grid();

        $this->metadataFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->with('some_grid')
            ->willReturn($metadata)
        ;
        $this->gridInitializer
            ->expects($this->once())
            ->method('initializeGrid')
            ->with($resource, $operation, $metadata)
            ->willReturn($metadata)
        ;
        $errors = $this->createMock(ConstraintViolationListInterface::class);
        $errors->expects($this->once())
            ->method('count')
            ->willReturn(1)
        ;

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($metadata)
            ->willReturn($errors)
        ;

        $this->expectExceptionObject(new InvalidGridException('some_grid', $errors));
        $this->factory->create('some_grid', $operation);
    }

    protected function setUp(): void
    {
        $this->metadataFactory = $this->createMock(GridMetadataFactoryInterface::class);
        $this->gridInitializer = $this->createMock(GridInitializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->factory = new GridFactory(
            $this->metadataFactory,
            $this->gridInitializer,
            $this->validator,
        );
    }
}
