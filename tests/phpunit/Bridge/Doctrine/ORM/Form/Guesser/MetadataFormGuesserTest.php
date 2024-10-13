<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Form\Guesser;

use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Persistence\Mapping\ClassMetadata;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Form\Guesser\MetadataFormGuesser;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Form\Guesser\FormGuesserInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Text;
use LAG\AdminBundle\Resource\Metadata\Update;
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
        $resource = new Resource(dataClass: \stdClass::class);
        $operation = (new Update())->withResource($resource);
        $property = new Text(propertyPath: 'name');

        $reflectionProperty = self::createMock(\ReflectionProperty::class);
        $reflectionClass = self::createMock(\ReflectionClass::class);
        $classMetadata = self::createMock(ClassMetadata::class);

        $reflectionClass->expects(self::once())
            ->method('hasProperty')
            ->with('name')
            ->willReturn(true)
        ;
        $reflectionClass->expects(self::once())
            ->method('getProperty')
            ->with('name')
            ->willReturn($reflectionProperty)
        ;
        $classMetadata->expects(self::once())
            ->method('getReflectionClass')
            ->willReturn($reflectionClass)
        ;
        $reflectionProperty->expects(self::once())
            ->method('getAttributes')
            ->with(GeneratedValue::class)
            ->willReturn([new GeneratedValue()])
        ;
        $this->metadataHelper
            ->expects(self::once())
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
        $resource = new Resource(dataClass: \stdClass::class);
        $operation = (new Update())->withResource($resource);
        $property = new Text(propertyPath: 'name');

        $reflectionClass = self::createMock(\ReflectionClass::class);
        $reflectionClass->expects(self::once())
            ->method('hasProperty')
            ->with('name')
            ->willReturn(false)
        ;
        $classMetadata = self::createMock(ClassMetadata::class);
        $classMetadata->expects(self::once())
            ->method('getReflectionClass')
            ->willReturn($reflectionClass)
        ;
        $this->metadataHelper
            ->expects(self::once())
            ->method('findMetadata')
            ->with(\stdClass::class)
            ->willReturn($classMetadata)
        ;
        $this->decorated
            ->expects(self::once())
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
            ->expects(self::once())
            ->method('findMetadata')
            ->with(\stdClass::class)
            ->willReturn(null)
        ;
        $resource = new Resource(dataClass: \stdClass::class);
        $operation = (new Update())->withResource($resource);
        $property = new Text();

        $this->decorated
            ->expects(self::once())
            ->method('guessFormType')
            ->with($operation, $property)
            ->willReturn('SomeFormType')
        ;

        $guessedType = $this->guesser->guessFormType($operation, $property);

        self::assertEquals('SomeFormType', $guessedType);
    }

    protected function setUp(): void
    {
        $this->decorated = self::createMock(FormGuesserInterface::class);
        $this->metadataHelper = self::createMock(MetadataHelperInterface::class);
        $this->guesser = new MetadataFormGuesser(
            $this->decorated,
            $this->metadataHelper,
        );
    }
}
