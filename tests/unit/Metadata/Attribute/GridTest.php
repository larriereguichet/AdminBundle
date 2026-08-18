<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Grid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GridTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $grid = new Grid(name: 'my_grid', properties: ['title']);

        self::assertSame('my_grid', $grid->getName());
        self::assertNull($grid->getTitle());
        self::assertNull($grid->getType());
        self::assertNull($grid->getTemplate());
        self::assertSame('lag:table_grid', $grid->getComponent());
        self::assertSame(['title'], $grid->getProperties());
        self::assertSame([], $grid->getAttributes());
        self::assertSame([], $grid->getRowAttributes());
        self::assertSame([], $grid->getHeaderRowAttributes());
        self::assertSame([], $grid->getHeaderAttributes());
        self::assertSame([], $grid->getOptions());
        self::assertSame([], $grid->getFormOptions());
        self::assertNull($grid->getEmptyMessage());
        self::assertFalse($grid->isSortable());
        self::assertSame('sort', $grid->getSortParameter());
        self::assertSame('order', $grid->getOrderParameter());
        self::assertTrue($grid->useHeaders());
    }

    #[Test]
    public function itHasNoPropertiesWhenEmpty(): void
    {
        $grid = new Grid(name: 'my_grid', properties: []);

        self::assertFalse($grid->hasProperties());
    }

    #[Test]
    public function itHasPropertiesWhenSet(): void
    {
        $grid = new Grid(name: 'my_grid', properties: ['title', 'author']);

        self::assertTrue($grid->hasProperties());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $grid = new Grid(name: 'my_grid', properties: ['title']);

        $new = $grid->withName('other_grid');
        self::assertNotSame($grid, $new);
        self::assertSame('other_grid', $new->getName());
        self::assertSame('my_grid', $grid->getName());

        $new = $grid->withTitle('My Books');
        self::assertNotSame($grid, $new);
        self::assertSame('My Books', $new->getTitle());

        $new = $grid->withType('card');
        self::assertNotSame($grid, $new);
        self::assertSame('card', $new->getType());

        $new = $grid->withTemplate('@App/my_grid.html.twig');
        self::assertNotSame($grid, $new);
        self::assertSame('@App/my_grid.html.twig', $new->getTemplate());

        $new = $grid->withComponent('lag:card_grid');
        self::assertNotSame($grid, $new);
        self::assertSame('lag:card_grid', $new->getComponent());

        $new = $grid->withProperties(['author', 'title']);
        self::assertNotSame($grid, $new);
        self::assertSame(['author', 'title'], $new->getProperties());

        $new = $grid->withAttributes(['class' => 'table table-striped']);
        self::assertNotSame($grid, $new);
        self::assertSame(['class' => 'table table-striped'], $new->getAttributes());

        $new = $grid->withRowAttributes(['data-id' => 'id']);
        self::assertNotSame($grid, $new);
        self::assertSame(['data-id' => 'id'], $new->getRowAttributes());

        $new = $grid->withHeaderRowAttributes(['class' => 'header']);
        self::assertNotSame($grid, $new);
        self::assertSame(['class' => 'header'], $new->getHeaderRowAttributes());

        $new = $grid->withHeaderAttributes(['scope' => 'col']);
        self::assertNotSame($grid, $new);
        self::assertSame(['scope' => 'col'], $new->getHeaderAttributes());

        $new = $grid->withOptions(['batch' => true]);
        self::assertNotSame($grid, $new);
        self::assertSame(['batch' => true], $new->getOptions());

        $new = $grid->withFormOptions(['method' => 'GET']);
        self::assertNotSame($grid, $new);
        self::assertSame(['method' => 'GET'], $new->getFormOptions());

        $new = $grid->withEmptyMessage('No books found');
        self::assertNotSame($grid, $new);
        self::assertSame('No books found', $new->getEmptyMessage());

        $new = $grid->withSortable(true);
        self::assertNotSame($grid, $new);
        self::assertTrue($new->isSortable());

        $new = $grid->withForm(null);
        self::assertNotSame($grid, $new);
        self::assertNull($new->getForm());

        $new = $grid->withTitleAttributes(['class' => 'grid-title']);
        self::assertNotSame($grid, $new);
        self::assertSame(['class' => 'grid-title'], $new->getTitleAttributes());
        self::assertSame([], $grid->getTitleAttributes());
    }

    #[Test]
    public function itMutatesSortAndOrderParameters(): void
    {
        $grid = new Grid(name: 'my_grid', properties: ['title']);

        $same = $grid->setSortParameter('s');
        self::assertSame($grid, $same);
        self::assertSame('s', $grid->getSortParameter());

        $same = $grid->setOrderParameter('o');
        self::assertSame($grid, $same);
        self::assertSame('o', $grid->getOrderParameter());
    }

    #[Test]
    public function itMutatesUseHeaders(): void
    {
        $grid = new Grid(name: 'my_grid', properties: ['title']);

        $grid->setUseHeaders(false);
        self::assertFalse($grid->useHeaders());
    }
}
