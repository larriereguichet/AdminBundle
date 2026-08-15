<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Twig\Component;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Twig\Component\AttributeComponentInterface;
use LAG\AdminBundle\Twig\Component\Grid;
use LAG\AdminBundle\Twig\Component\TableGrid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Runtime\EscaperRuntime;

final class TableGridTest extends TestCase
{
    #[Test]
    public function itExposesTheGridAttributesAsAnArray(): void
    {
        $component = new TableGrid();
        $component->grid = $this->createGridView(['class' => 'striped', 'data-controller' => 'sortable']);

        // AttributeComponentRenderListener merges the returned array into the component ComponentAttributes, and
        // the interface declares an array: returning the ComponentAttributes object itself raises a TypeError on
        // every render, which is exactly what happened to the row component
        self::assertSame(['class' => 'striped', 'data-controller' => 'sortable'], $component->getAttributes());
    }

    #[Test]
    public function itIsAnAttributeComponent(): void
    {
        self::assertInstanceOf(AttributeComponentInterface::class, new TableGrid());
        self::assertInstanceOf(AttributeComponentInterface::class, new Grid());
    }

    #[Test]
    public function itExposesNoAttributeWhenTheGridDeclaresNone(): void
    {
        $component = new TableGrid();
        $component->grid = $this->createGridView([]);

        self::assertSame([], $component->getAttributes());
    }

    /** @param array<string, mixed> $attributes */
    private function createGridView(array $attributes): GridView
    {
        return new GridView(
            name: 'books',
            type: 'table',
            rows: [],
            attributes: new ComponentAttributes($attributes, new EscaperRuntime()),
        );
    }
}
