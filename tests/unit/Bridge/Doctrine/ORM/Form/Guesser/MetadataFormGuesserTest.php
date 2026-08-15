<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Bridge\Doctrine\ORM\Form\Guesser;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\GeneratedValue;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Form\Guesser\MetadataFormGuesser;
use LAG\AdminBundle\Form\Guesser\FormGuesserInterface;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Metadata\Attribute\Update;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MetadataFormGuesserTest extends TestCase
{
    private MetadataFormGuesser $guesser;
    private MockObject $decorated;
    private MockObject $entityManager;

    #[Test]
    public function itDoesNotGuessFormOnGeneratedValues(): void
    {
        $resource = new Resource(resourceClass: \stdClass::class);
        $operation = (new Update())->setResource($resource);
        $property = new Text(propertyPath: 'name');

        $reflectionProperty = $this->createMock(\ReflectionProperty::class);
        $reflectionClass = $this->createMock(\ReflectionClass::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $metadataFactory = $this->createMock(ClassMetadataFactory::class);

        $reflectionClass->expects($this->once())
            ->method('hasProperty')
            ->with('name')
            ->willReturn(true)
        ;
        $reflectionClass->expects($this->once())
            ->method('getProperty')
            ->with('name')
            ->willReturn($reflectionProperty)
        ;
        $classMetadata->expects($this->once())
            ->method('getReflectionClass')
            ->willReturn($reflectionClass)
        ;
        $reflectionProperty->expects($this->once())
            ->method('getAttributes')
            ->with(GeneratedValue::class)
            ->willReturn([new GeneratedValue()])
        ;
        $metadataFactory->expects($this->once())
            ->method('hasMetadataFor')
            ->with(\stdClass::class)
            ->willReturn(false)
        ;
        $metadataFactory->expects($this->once())
            ->method('getMetadataFor')
            ->with(\stdClass::class)
            ->willReturn($classMetadata)
        ;
        $this->entityManager
            ->expects($this->once())
            ->method('getMetadataFactory')
            ->willReturn($metadataFactory)
        ;
        $this->decorated
            ->expects($this->never())
            ->method('guessFormType')
        ;

        $guessedType = $this->guesser->guessFormType($operation, $property);

        self::assertNull($guessedType);
    }

    #[Test]
    public function itDoesNotGuessFormOnNotReadableProperty(): void
    {
        $resource = new Resource(resourceClass: \stdClass::class);
        $operation = (new Update())->setResource($resource);
        $property = new Text(propertyPath: 'name');

        $reflectionClass = $this->createMock(\ReflectionClass::class);
        $reflectionClass->expects($this->once())
            ->method('hasProperty')
            ->with('name')
            ->willReturn(false)
        ;
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->expects($this->once())
            ->method('getReflectionClass')
            ->willReturn($reflectionClass)
        ;
        $metadataFactory = $this->createMock(ClassMetadataFactory::class);
        $metadataFactory->expects($this->once())
            ->method('hasMetadataFor')
            ->with(\stdClass::class)
            ->willReturn(false)
        ;
        $metadataFactory->expects($this->once())
            ->method('getMetadataFor')
            ->with(\stdClass::class)
            ->willReturn($classMetadata)
        ;
        $this->entityManager
            ->expects($this->once())
            ->method('getMetadataFactory')
            ->willReturn($metadataFactory)
        ;
        $this->decorated
            ->expects($this->once())
            ->method('guessFormType')
            ->with($operation, $property)
            ->willReturn('SomeFormType')
        ;

        $guessedType = $this->guesser->guessFormType($operation, $property);

        self::assertEquals('SomeFormType', $guessedType);
    }

    #[Test]
    public function itDoesNotGuessFormWithoutMetadata(): void
    {
        $resource = new Resource(resourceClass: \stdClass::class);
        $operation = (new Update())->setResource($resource);
        $property = new Text();

        $metadataFactory = $this->createMock(ClassMetadataFactory::class);
        $metadataFactory->expects($this->once())
            ->method('hasMetadataFor')
            ->with(\stdClass::class)
            ->willReturn(true)
        ;
        $this->entityManager
            ->expects($this->once())
            ->method('getMetadataFactory')
            ->willReturn($metadataFactory)
        ;
        $this->decorated
            ->expects($this->once())
            ->method('guessFormType')
            ->with($operation, $property)
            ->willReturn('SomeFormType')
        ;

        $guessedType = $this->guesser->guessFormType($operation, $property);

        self::assertEquals('SomeFormType', $guessedType);
    }

    #[Test]
    public function itDelegatesToDecoratedWhenPropertyPathIsNotString(): void
    {
        $resource = new Resource(resourceClass: \stdClass::class);
        $operation = (new Update())->setResource($resource);
        $property = new Text(propertyPath: false);

        $reflectionClass = $this->createMock(\ReflectionClass::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->method('getReflectionClass')->willReturn($reflectionClass);
        $metadataFactory = $this->createMock(ClassMetadataFactory::class);
        $metadataFactory->method('hasMetadataFor')->willReturn(false);
        $metadataFactory->method('getMetadataFor')->willReturn($classMetadata);
        $this->entityManager->method('getMetadataFactory')->willReturn($metadataFactory);

        $this->decorated
            ->expects($this->once())
            ->method('guessFormType')
            ->with($operation, $property)
            ->willReturn('SomeType')
        ;

        $result = $this->guesser->guessFormType($operation, $property);

        self::assertSame('SomeType', $result);
    }

    #[Test]
    public function itDelegatesToDecoratedWhenPropertyHasNoGeneratedValue(): void
    {
        $resource = new Resource(resourceClass: \stdClass::class);
        $operation = (new Update())->setResource($resource);
        $property = new Text(propertyPath: 'name');

        $reflectionProperty = $this->createMock(\ReflectionProperty::class);
        $reflectionProperty->method('getAttributes')->with(GeneratedValue::class)->willReturn([]);
        $reflectionClass = $this->createMock(\ReflectionClass::class);
        $reflectionClass->method('hasProperty')->with('name')->willReturn(true);
        $reflectionClass->method('getProperty')->with('name')->willReturn($reflectionProperty);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->method('getReflectionClass')->willReturn($reflectionClass);
        $metadataFactory = $this->createMock(ClassMetadataFactory::class);
        $metadataFactory->method('hasMetadataFor')->willReturn(false);
        $metadataFactory->method('getMetadataFor')->willReturn($classMetadata);
        $this->entityManager->method('getMetadataFactory')->willReturn($metadataFactory);

        $this->decorated
            ->expects($this->once())
            ->method('guessFormType')
            ->with($operation, $property)
            ->willReturn('SomeType')
        ;

        $result = $this->guesser->guessFormType($operation, $property);

        self::assertSame('SomeType', $result);
    }

    #[Test]
    public function itDelegatesGuessFormOptions(): void
    {
        $resource = new Resource(resourceClass: \stdClass::class);
        $operation = (new Update())->setResource($resource);
        $property = new Text(propertyPath: 'name');
        $expectedOptions = ['required' => false, 'property_path' => 'name'];

        $this->decorated
            ->expects($this->once())
            ->method('guessFormOptions')
            ->with($operation, $property)
            ->willReturn($expectedOptions)
        ;

        $options = $this->guesser->guessFormOptions($operation, $property);

        self::assertEquals($expectedOptions, $options);
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(FormGuesserInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->guesser = new MetadataFormGuesser(
            $this->decorated,
            $this->entityManager,
        );
    }
}
