<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Filter;
use LAG\AdminBundle\Metadata\Attribute\Index;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CollectionOperationTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $operation = new Index();

        self::assertTrue($operation->hasPagination());
        self::assertSame(25, $operation->getItemsPerPage());
        self::assertSame('page', $operation->getPageParameter());
        self::assertNull($operation->getGrid());
        self::assertSame([], $operation->getGridOptions());
        self::assertNull($operation->getFilterForm());
        self::assertSame([], $operation->getFilterFormOptions());
        self::assertSame([], $operation->getBatchOperations());
        self::assertNull($operation->getCollectionLinks());
    }

    #[Test]
    public function itHasNoFiltersWhenEmptyArray(): void
    {
        $operation = new Index(filters: []);

        self::assertFalse($operation->hasFilters());
    }

    #[Test]
    public function itHasFiltersWhenSet(): void
    {
        $filter = new Filter(name: 'search');
        $operation = new Index(filters: [$filter]);

        self::assertTrue($operation->hasFilters());
        self::assertTrue($operation->hasFilter('search'));
        self::assertFalse($operation->hasFilter('missing'));
    }

    #[Test]
    public function itGetsFilterByName(): void
    {
        $filter = new Filter(name: 'search');
        $operation = new Index(filters: [$filter]);

        self::assertSame($filter, $operation->getFilter('search'));
        self::assertNull($operation->getFilter('missing'));
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $operation = new Index();

        $new = $operation->withPagination(false);
        self::assertNotSame($operation, $new);
        self::assertFalse($new->hasPagination());
        self::assertTrue($operation->hasPagination());

        $new = $operation->withItemsPerPage(50);
        self::assertNotSame($operation, $new);
        self::assertSame(50, $new->getItemsPerPage());

        $new = $operation->withPageParameter('p');
        self::assertNotSame($operation, $new);
        self::assertSame('p', $new->getPageParameter());

        $filter = new Filter(name: 'search');
        $new = $operation->withFilters([$filter]);
        self::assertNotSame($operation, $new);
        self::assertSame([$filter], $new->getFilters());

        $new = $operation->withFilter($filter);
        self::assertNotSame($operation, $new);
        self::assertSame([$filter], $new->getFilters());

        $new = $operation->withGrid('my_grid');
        self::assertNotSame($operation, $new);
        self::assertSame('my_grid', $new->getGrid());

        $new = $operation->withGridOptions(['batch' => true]);
        self::assertNotSame($operation, $new);
        self::assertSame(['batch' => true], $new->getGridOptions());

        $new = $operation->withFilterForm('App\Form\SearchType');
        self::assertNotSame($operation, $new);
        self::assertSame('App\Form\SearchType', $new->getFilterForm());

        $new = $operation->withFilterFormOptions(['method' => 'GET']);
        self::assertNotSame($operation, $new);
        self::assertSame(['method' => 'GET'], $new->getFilterFormOptions());

        $new = $operation->withBatchOperations(['delete']);
        self::assertNotSame($operation, $new);
        self::assertSame(['delete'], $new->getBatchOperations());

        $new = $operation->withCollectionLinks([]);
        self::assertNotSame($operation, $new);
        self::assertSame([], $new->getCollectionLinks());
    }
}
