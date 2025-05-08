<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\PropertyGuesser;

use LAG\AdminBundle\Metadata\Date;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Metadata\Text;
use LAG\AdminBundle\Resource\PropertyGuesser\PropertyGuesser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PropertyGuesserTest extends TestCase
{
    private PropertyGuesser $propertyGuesser;

    #[Test]
    #[DataProvider(methodName: 'propertyTypes')]
    public function itGuessProperty(string $propertyName, ?string $propertyType, ?PropertyInterface $expectedProperty): void
    {
        $property = $this->propertyGuesser->guessProperty(\stdClass::class, $propertyName, $propertyType);

        self::assertEquals($expectedProperty, $property);
    }

    public static function propertyTypes(): iterable
    {
        yield 'string_property' => ['name', 'string', new Text(name: 'name')];
        yield 'integer_property' => ['name', 'integer', new Text(name: 'name')];
        yield 'float_property' => ['name', 'float', new Text(name: 'name')];
        yield 'date_property' => ['name', \DateTime::class, new Date(name: 'name')];
        yield 'date_immutable_property' => ['name', \DateTimeImmutable::class, new Date(name: 'name')];
        yield 'std_class_property' => ['name', \stdClass::class, null];
        yield 'null_property' => ['name', 'null', null];
    }

    protected function setUp(): void
    {
        $this->propertyGuesser = new PropertyGuesser();
    }
}
