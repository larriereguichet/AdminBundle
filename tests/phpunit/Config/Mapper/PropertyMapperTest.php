<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Config\Mapper;

use LAG\AdminBundle\Config\Mapper\PropertyMapper;
use LAG\AdminBundle\Metadata\Collection;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Metadata\Text;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PropertyMapperTest extends TestCase
{
    #[Test]
    #[DataProvider(methodName: 'propertiesData')]
    public function itMapsAnArrayToAProperty(array $data, PropertyInterface $expectedProperty): void
    {
        $mapper = new PropertyMapper();
        $property = $mapper->fromArray($data);

        self::assertEquals($expectedProperty, $property);
    }

    public static function propertiesData(): iterable
    {
        $data = [
            'name' => 'my_text',
            '__class__' => Text::class,
        ];
        $text = new Text(
            name: 'my_text',
        );
        yield 'text' => [$data, $text];

        $data = [
            'name' => 'My Collection',
            'entry_property' => [
                '__class__' => Text::class,
                'name' => 'My Name Property',
            ],
            '__class__' => Collection::class,
        ];
        $collection = new Collection(
            name: 'My Collection',
            entryProperty: new Text(name: 'My Name Property'),
        );

        yield 'collection' => [$data, $collection];
    }
}
