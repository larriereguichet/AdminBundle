<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Metadata;

use Doctrine\Persistence\Mapping\ClassMetadata;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactory;
use LAG\AdminBundle\Resource\Metadata\Boolean;
use LAG\AdminBundle\Resource\Metadata\Count;
use LAG\AdminBundle\Resource\Metadata\Date;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use LAG\AdminBundle\Resource\Metadata\Text;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function Symfony\Component\String\u;

final class MetadataPropertyFactoryTest extends TestCase
{
    private MetadataPropertyFactory $factory;
    private MockObject $metadataHelper;

    #[Test]
    public function itCreatesProperties(): void
    {
        $types = [
            'my_date' => 'datetime',
            'my_boolean' => 'boolean',
            'my_string' => 'string',
        ];
        $metadata = self::createMock(ClassMetadata::class);
        $metadata
            ->expects(self::once())
            ->method('getFieldNames')
            ->willReturn(array_keys($types))
        ;
        $metadata
            ->expects(self::once())
            ->method('getAssociationNames')
            ->willReturn([
                'my_association',
            ])
        ;
        $metadata
            ->expects(self::exactly(\count($types)))
            ->method('getTypeOfField')
            ->willReturnCallback(fn ($type) => $types[$type])
        ;
        $metadata
            ->expects(self::once())
            ->method('isCollectionValuedAssociation')
            ->willReturnCallback(function () {
                return true;
            })
        ;

        $this
            ->metadataHelper
            ->expects(self::once())
            ->method('findMetadata')
            ->with('MyClass')
            ->willReturn($metadata)
        ;

        $properties = $this->factory->createProperties('MyClass');
        $this->assertCount(\count($types) + 1, $properties);

        foreach ($properties as $property) {
            $this->assertInstanceOf(PropertyInterface::class, $property);

            if ($property->getName() === 'my_string') {
                $this->assertInstanceOf(Text::class, $property);
            } elseif ($property->getName() === 'my_boolean') {
                $this->assertInstanceOf(Boolean::class, $property);
            } elseif ($property->getName() === 'my_date') {
                $this->assertInstanceOf(Date::class, $property);
            } elseif ($property->getName() === 'my_association') {
                $this->assertInstanceOf(Count::class, $property);
            } else {
                $this->fail();
            }
        }
    }

    #[Test]
    public function itCreatesPropertiesWithTooManyFields(): void
    {
        $types = [
            'my_date' => 'datetime',
            'my_boolean' => 'boolean',
            'my_string' => 'string',
            'my_string1' => 'string',
            'my_string2' => 'string',
            'my_string3' => 'string',
            'my_string4' => 'string',
            'my_string5' => 'string',
            'my_string6' => 'string',
            'my_string7' => 'string',
            'my_string8' => 'string',
        ];
        $metadata = self::createMock(ClassMetadata::class);
        $metadata
            ->expects(self::once())
            ->method('getFieldNames')
            ->willReturn(array_keys($types))
        ;
        $metadata
            ->expects($this->never())
            ->method('getAssociationNames')
        ;
        $metadata
            ->expects(self::exactly(\count($types)))
            ->method('getTypeOfField')
            ->willReturnCallback(fn ($type) => $types[$type])
        ;

        $this
            ->metadataHelper
            ->expects(self::once())
            ->method('findMetadata')
            ->with('MyClass')
            ->willReturn($metadata)
        ;

        $properties = $this->factory->createProperties('MyClass');
        $this->assertCount(\count($types), $properties);

        foreach ($properties as $property) {
            $this->assertInstanceOf(PropertyInterface::class, $property);

            if (u($property->getName())->startsWith('my_string')) {
                $this->assertInstanceOf(Text::class, $property);
            } elseif ($property->getName() === 'my_boolean') {
                $this->assertInstanceOf(Boolean::class, $property);
            } elseif ($property->getName() === 'my_date') {
                $this->assertInstanceOf(Date::class, $property);
            } elseif ($property->getName() === 'my_association') {
                $this->assertInstanceOf(Count::class, $property);
            } else {
                $this->fail();
            }
        }
    }

    #[Test]
    public function itCreatesPropertiesWithTooManyAssociationFields(): void
    {
        $types = [
            'my_date' => 'datetime',
            'my_boolean' => 'boolean',
            'my_string' => 'string',
            'my_string1' => 'string',
            'my_string2' => 'string',
            'my_string3' => 'string',
            'my_string4' => 'string',
            'my_string5' => 'string',
            'my_string6' => 'string',
            'my_string7' => 'string',
            'my_string8' => 'string',
        ];
        $metadata = self::createMock(ClassMetadata::class);
        $metadata
            ->expects(self::once())
            ->method('getFieldNames')
            ->willReturn([])
        ;
        $metadata
            ->expects(self::once())
            ->method('getAssociationNames')
            ->willReturn(array_keys($types))
        ;
        $metadata
            ->expects(self::exactly(\count($types)))
            ->method('isCollectionValuedAssociation')
            ->willReturnCallback(function () {
                return true;
            })
        ;

        $this
            ->metadataHelper
            ->expects(self::once())
            ->method('findMetadata')
            ->with('MyClass')
            ->willReturn($metadata)
        ;

        $properties = $this->factory->createProperties('MyClass');
        $this->assertCount(\count($types), $properties);

        foreach ($properties as $property) {
            $this->assertInstanceOf(Count::class, $property);
        }
    }

    #[Test]
    public function itCreatesPropertiesWithoutMetadata(): void
    {
        $this
            ->metadataHelper
            ->expects(self::once())
            ->method('findMetadata')
            ->with('MyClass')
            ->willReturn(null)
        ;
        $properties = $this->factory->createProperties('MyClass');

        $this->assertCount(0, $properties);
    }

    protected function setUp(): void
    {
        $this->metadataHelper = self::createMock(MetadataHelperInterface::class);
        $this->factory = new MetadataPropertyFactory($this->metadataHelper);
    }
}
