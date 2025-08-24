<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Form\Guesser;

use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Persistence\Mapping\ClassMetadata;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Form\Guesser\MetadataFormGuesser;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Form\Guesser\FormGuesserInterface;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Text;
use LAG\AdminBundle\Metadata\Update;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MetadataFormGuesserTest extends TestCase
{
    private MetadataFormGuesser $guesser;
    private MockObject $decorated;
    private MockObject $metadataHelper;

    #[Test]
    public function itDoesNotGuessFormOnGeneratedValues(): void
    {
        $resource = new Resource(resourceClass: \stdClass::class);
        $operation = (new Update())->setResource($resource);
        $property = new Text(propertyPath: 'name');

        $reflectionProperty = $this->createMock(\ReflectionProperty::class);
        $reflectionClass = $this->createMock(\ReflectionClass::class);
        $classMetadata = $this->createMock(ClassMetadata::class);

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
        $this->metadataHelper
            ->expects($this->once())
            ->method('findMetadata')
            ->with(\stdClass::class)
            ->willReturn($classMetadata)
        ;
        $this->decorated
            ->expects(self::never())
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
        $this->metadataHelper
            ->expects($this->once())
            ->method('findMetadata')
            ->with(\stdClass::class)
            ->willReturn($classMetadata)
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
        $this->metadataHelper
            ->expects($this->once())
            ->method('findMetadata')
            ->with(\stdClass::class)
            ->willReturn(null)
        ;
        $resource = new Resource(resourceClass: \stdClass::class);
        $operation = (new Update())->setResource($resource);
        $property = new Text();

        $this->decorated
            ->expects($this->once())
            ->method('guessFormType')
            ->with($operation, $property)
            ->willReturn('SomeFormType')
        ;

        $guessedType = $this->guesser->guessFormType($operation, $property);

        self::assertEquals('SomeFormType', $guessedType);
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(FormGuesserInterface::class);
        $this->metadataHelper = $this->createMock(MetadataHelperInterface::class);
        $this->guesser = new MetadataFormGuesser(
            $this->decorated,
            $this->metadataHelper,
        );
    }
}
