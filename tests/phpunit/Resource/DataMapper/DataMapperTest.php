<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\DataMapper;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\DataMapper\DataMapper;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use LAG\AdminBundle\Resource\Metadata\Text;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DataMapperTest extends TestCase
{
    private DataMapperInterface $dataMapper;

    #[Test]
    #[DataProvider(methodName: 'values')]
    public function itMapsPropertyData(PropertyInterface $property, mixed $data, mixed $expectedValue): void
    {
        $value = $this->dataMapper->getValue($property, $data);

        self::assertEquals($expectedValue, $value);
    }

    #[Test]
    public function itDoesNotMapNotReadableData(): void
    {
        $data = new \stdClass();
        $data->name = 'Some name';

        self::expectExceptionObject(new Exception('The property path "wrongPath" is not readable in data of type "stdClass"'));
        $this->dataMapper->getValue(new Text(name: 'some', propertyPath: 'wrongPath'), $data);
    }

    public static function values(): iterable
    {
        $data = new \stdClass();
        $data->id = 666;
        $data->name = 'My beautiful data';

        yield 'simple_property_path' => [new Text(name: 'some', propertyPath: 'name'), $data, $data->name];

        $linkedData = new \stdClass();
        $linkedData->label = 'A meaningless value';
        $data = new \stdClass();
        $data->id = 666;
        $data->linked = $linkedData;

        yield 'compound_property_path' => [new Text(name: 'some', propertyPath: 'linked.label'), $data, $linkedData->label];

        $data = new \stdClass();
        $data->id = 666;

        yield 'object' => [new Text(name: 'some', propertyPath: true), $data, $data];

        $data = new \stdClass();
        $data->id = 666;

        yield 'no_mapping' => [new Text(name: 'some', propertyPath: false), $data, null];
    }

    protected function setUp(): void
    {
        $this->dataMapper = new DataMapper();
    }
}
