<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Filter\Resolver;

use LAG\AdminBundle\Filter\Resolver\FilterValuesResolver;
use LAG\AdminBundle\Filter\Resolver\FilterValuesResolverInterface;
use LAG\AdminBundle\Resource\Metadata\TextFilter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FilterResolverTest extends TestCase
{
    private FilterValuesResolverInterface $filterResolver;

    #[Test]
    #[DataProvider(methodName: 'filters')]
    public function itResolvesFilters(iterable $filters, array $context, array $result): void
    {
        $data = $this->filterResolver->resolveFilters($filters, $context);

        self::assertEquals($result, $data);
    }

    public static function filters(): iterable
    {
        yield 'empty_context' => [[], [], []];
        yield 'string_filter' => [[
            new TextFilter(name: 'my_text_filter'),
        ], [
            'filters' => [
                'my_text_filter' => 'some_value',
                'another_filter' => 'another_value',
            ],
            'another_value' => 'another_value',
        ], [
            'my_text_filter' => 'some_value',
        ]];
        yield 'several_filters' => [[
            new TextFilter(name: 'my_text_filter'),
            new TextFilter(name: 'my_other_filter'),
        ], [
            'filters' => [
                'my_text_filter' => 'some_value',
                'my_other_filter' => 'another_value',
            ],
            'another_value' => 'another_value',
        ], [
            'my_text_filter' => 'some_value',
            'my_other_filter' => 'another_value',
        ]];
    }

    protected function setUp(): void
    {
        $this->filterResolver = new FilterValuesResolver();
    }
}
