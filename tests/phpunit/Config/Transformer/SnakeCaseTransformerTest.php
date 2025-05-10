<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Config\Transformer;

use LAG\AdminBundle\Config\Transformer\SnakeCaseTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SnakeCaseTransformerTest extends TestCase
{
    #[Test]
    #[DataProvider(methodName: 'sources')]
    public function itTransformsASource(mixed $source, mixed $expectedTarget): void
    {
        $transformer = new SnakeCaseTransformer();
        $target = $transformer(new \stdClass(), fn () => $source);

        self::assertSame($expectedTarget, $target);
    }

    public static function sources(): iterable
    {
        $source = [
            'someKey' => 'some_value',
            'anotherKey' => 'another_value',
            'key' => 'value',
            'integer_key',
        ];
        $target = [
            'some_key' => 'some_value',
            'another_key' => 'another_value',
            'key' => 'value',
            'integer_key',
        ];

        yield [$source, $target];

        $source = new \stdClass();
        $target = $source;

        yield [$source, $target];
    }
}
