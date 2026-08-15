<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\Factory;

use LAG\AdminBundle\Exception\InvalidGridException;
use LAG\AdminBundle\Grid\Factory\GridFactory;
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
    private MockObject $validator;

    #[Test]
    public function itCreatesAGrid(): void
    {
        $operation = new Index()->setResource(new Resource());
        $metadata = new Grid();

        $this->metadataFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->with('some_grid')
            ->willReturn($metadata)
        ;
        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations->expects($this->once())
            ->method('count')
            ->willReturn(0)
        ;
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn($violations)
        ;

        $grid = $this->factory->create('some_grid', $operation);

        self::assertEquals($metadata, $grid);
    }

    #[Test]
    public function itDoesNotCreateInvalidGrid(): void
    {
        $operation = new Index()->setResource(new Resource());
        $metadata = new Grid();

        $this->metadataFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->with('some_grid')
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
            ->willReturn($errors)
        ;

        $this->expectExceptionObject(new InvalidGridException('some_grid', $errors));
        $this->factory->create('some_grid', $operation);
    }

    protected function setUp(): void
    {
        $this->metadataFactory = $this->createMock(GridMetadataFactoryInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->factory = new GridFactory(
            $this->metadataFactory,
            $this->validator,
        );
    }
}
