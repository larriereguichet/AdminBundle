<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Metadata;

use Doctrine\Persistence\Mapping\ClassMetadata;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactory;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactoryInterface;
use LAG\AdminBundle\Metadata\Property\BooleanProperty;
use LAG\AdminBundle\Metadata\Property\CountProperty;
use LAG\AdminBundle\Metadata\Property\DateProperty;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use LAG\AdminBundle\Metadata\Property\StringProperty;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use function Symfony\Component\String\u;

class MetadataPropertyFactoryTest extends TestCase
{
    private MetadataPropertyFactoryInterface $factory;
    private MockObject $metadataHelper;

    public function testCreateProperties(): void
    {
        $types = [
            'my_date' => 'datetime',
            'my_boolean' => 'boolean',
            'my_string' => 'string',
        ];
        $metadata = $this->createMock(ClassMetadata::class);
        $metadata
            ->expects($this->once())
            ->method('getFieldNames')
            ->willReturn(array_keys($types))
        ;
        $metadata
            ->expects($this->once())
            ->method('getAssociationNames')
            ->willReturn([
                'my_association',
            ])
        ;
        $metadata
            ->expects($this->exactly(count($types)))
            ->method('getTypeOfField')
            ->willReturnCallback(fn($type) => $types[$type])
        ;

        $this
            ->metadataHelper
            ->expects($this->once())
            ->method('findMetadata')
            ->with('MyClass')
            ->willReturn($metadata)
        ;

        $properties = $this->factory->createProperties('MyClass');
        $this->assertCount(count($types) + 1, $properties);

        foreach ($properties as $property) {
            $this->assertInstanceOf(PropertyInterface::class, $property);

            if ($property->getName() === 'my_string') {
                $this->assertInstanceOf(StringProperty::class, $property);
            } elseif ($property->getName() === 'my_boolean') {
                $this->assertInstanceOf(BooleanProperty::class, $property);
            } elseif ($property->getName() === 'my_date') {
                $this->assertInstanceOf(DateProperty::class, $property);
            } elseif ($property->getName() === 'my_association') {
                $this->assertInstanceOf(CountProperty::class, $property);
            } else {
                $this->fail();
            }
        }
    }

    public function testCreatePropertiesWithTooManyFields(): void
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
        $metadata = $this->createMock(ClassMetadata::class);
        $metadata
            ->expects($this->once())
            ->method('getFieldNames')
            ->willReturn(array_keys($types))
        ;
        $metadata
            ->expects($this->never())
            ->method('getAssociationNames')
        ;
        $metadata
            ->expects($this->exactly(count($types)))
            ->method('getTypeOfField')
            ->willReturnCallback(fn($type) => $types[$type])
        ;

        $this
            ->metadataHelper
            ->expects($this->once())
            ->method('findMetadata')
            ->with('MyClass')
            ->willReturn($metadata)
        ;

        $properties = $this->factory->createProperties('MyClass');
        $this->assertCount(count($types), $properties);

        foreach ($properties as $property) {
            $this->assertInstanceOf(PropertyInterface::class, $property);

            if (u($property->getName())->startsWith('my_string')) {
                $this->assertInstanceOf(StringProperty::class, $property);
            } elseif ($property->getName() === 'my_boolean') {
                $this->assertInstanceOf(BooleanProperty::class, $property);
            } elseif ($property->getName() === 'my_date') {
                $this->assertInstanceOf(DateProperty::class, $property);
            } elseif ($property->getName() === 'my_association') {
                $this->assertInstanceOf(CountProperty::class, $property);
            } else {
                $this->fail();
            }
        }
    }

    public function testCreatePropertiesWithTooManyAssociationFields(): void
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
        $metadata = $this->createMock(ClassMetadata::class);
        $metadata
            ->expects($this->once())
            ->method('getFieldNames')
            ->willReturn([])
        ;
        $metadata
            ->expects($this->once())
            ->method('getAssociationNames')
            ->willReturn(array_keys($types))
        ;

        $this
            ->metadataHelper
            ->expects($this->once())
            ->method('findMetadata')
            ->with('MyClass')
            ->willReturn($metadata)
        ;

        $properties = $this->factory->createProperties('MyClass');
        $this->assertCount(count($types), $properties);

        foreach ($properties as $property) {
            $this->assertInstanceOf(CountProperty::class, $property);
        }
    }

    public function testCreatePropertiesWithoutMetadata(): void
    {
        $this
            ->metadataHelper
            ->expects($this->once())
            ->method('findMetadata')
            ->with('MyClass')
            ->willReturn(null)
        ;
        $properties = $this->factory->createProperties('MyClass');

        $this->assertCount(0, $properties);
    }

    public function testService()
    {
        $this->assertServiceExists(MetadataPropertyFactoryInterface::class);
    }

    protected function setUp(): void
    {
        $this->metadataHelper = $this->createMock(MetadataHelperInterface::class);
        $this->factory = new MetadataPropertyFactory($this->metadataHelper);
    }
}
