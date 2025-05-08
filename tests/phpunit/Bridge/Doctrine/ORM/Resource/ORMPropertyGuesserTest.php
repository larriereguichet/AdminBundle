<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Resource;

use Doctrine\Persistence\Mapping\ClassMetadata;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Resource\ORMPropertyGuesser;
use LAG\AdminBundle\Metadata\Boolean;
use LAG\AdminBundle\Metadata\Date;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Metadata\RichText;
use LAG\AdminBundle\Metadata\Text;
use LAG\AdminBundle\Resource\PropertyGuesser\PropertyGuesserInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ORMPropertyGuesserTest extends TestCase
{
    private ORMPropertyGuesser $propertyGuesser;
    private MockObject $decoratedPropertyGuesser;
    private MockObject $metadataHelper;

    #[Test]
    public function itDoesNotGuessPropertiesWithoutDoctrineMetadata(): void
    {
        $this->metadataHelper
            ->expects(self::once())
            ->method('findMetadata')
            ->with('SomeClass')
            ->willReturn(null)
        ;
        $this->decoratedPropertyGuesser
            ->expects(self::once())
            ->method('guessProperty')
            ->with('SomeClass', 'name', null)
            ->willReturn(null)
        ;
        $property = $this->propertyGuesser->guessProperty('SomeClass', 'name', null);

        self::assertNull($property);
    }

    #[Test]
    public function itDoesNotGuessPropertiesWithoutDoctrineField(): void
    {
        $classMetadata = self::createMock(ClassMetadata::class);

        $this->metadataHelper
            ->expects(self::once())
            ->method('findMetadata')
            ->with('SomeClass')
            ->willReturn($classMetadata)
        ;
        $classMetadata->expects(self::once())
            ->method('hasField')
            ->with('name')
            ->willReturn(false)
        ;
        $this->decoratedPropertyGuesser
            ->expects(self::once())
            ->method('guessProperty')
            ->with('SomeClass', 'name', null)
            ->willReturn(null)
        ;
        $property = $this->propertyGuesser->guessProperty('SomeClass', 'name', null);

        self::assertNull($property);
    }

    #[Test]
    #[DataProvider(methodName: 'propertyTypes')]
    public function itGuessProperties(?string $propertyType, ?PropertyInterface $expectedProperty): void
    {
        $classMetadata = self::createMock(ClassMetadata::class);

        $this->metadataHelper
            ->expects(self::once())
            ->method('findMetadata')
            ->with('SomeClass')
            ->willReturn($classMetadata)
        ;
        $classMetadata->expects(self::once())
            ->method('hasField')
            ->with('name')
            ->willReturn(true)
        ;
        $classMetadata->expects(self::once())
            ->method('getTypeOfField')
            ->with('name')
            ->willReturn($propertyType)
        ;

        $property = $this->propertyGuesser->guessProperty('SomeClass', 'name', $propertyType);

        self::assertEquals($expectedProperty, $property);
    }

    public static function propertyTypes(): iterable
    {
        yield 'string' => ['string', new Text(name: 'name')];
        yield 'text' => ['text', new RichText(name: 'name')];
        yield 'boolean' => ['boolean', new Boolean(name: 'name')];
        yield 'date' => ['date', new Date(name: 'name')];
        yield 'datetime' => ['date', new Date(name: 'name')];
        yield 'date_immutable' => ['date', new Date(name: 'name')];
        yield 'datetime_immutable' => ['date', new Date(name: 'name')];
        yield 'null' => ['null', null];
    }

    protected function setUp(): void
    {
        $this->decoratedPropertyGuesser = self::createMock(PropertyGuesserInterface::class);
        $this->metadataHelper = self::createMock(MetadataHelperInterface::class);
        $this->propertyGuesser = new ORMPropertyGuesser(
            $this->decoratedPropertyGuesser,
            $this->metadataHelper,
        );
    }
}
