<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\ViewBuilder\HeaderViewBuilder;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Text;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;

final class HeaderViewBuilderTest extends TestCase
{
    private HeaderViewBuilder $headerViewBuilder;

    #[Test]
    public function itBuildAHeader(): void
    {
        $operation = new Index(shortName: 'my_operation');
        $grid = new Grid(
            name: 'my_grid',
            translationDomain: 'a_domain',
            headerTemplate: 'my_header_template.html.twig',
            sortable: true,
        );
        $property = new Text(
            name: 'my_property',
            label: 'Some property',
            sortable: true,
            headerAttributes: ['class' => 'a-class'],
        );

        $headerView = $this->headerViewBuilder->buildHeader(
            operation: $operation,
            grid: $grid,
            property: $property,
            context: [
                'some_context' => 'some_value',
                'sort' => 'name',
                'order' => 'desc',
            ],
        );

        self::assertEquals('my_property', $headerView->name);
        self::assertEquals('my_header_template.html.twig', $headerView->template);
        self::assertEquals('Some property', $headerView->label);
        self::assertEquals('a_domain', $headerView->translationDomain);
        self::assertEquals('name', $headerView->sort);
        self::assertEquals('desc', $headerView->order);
        self::assertTrue($headerView->sortable);
        self::assertEquals(new ComponentAttributes(['class' => 'a-class']), $headerView->attributes);
    }

    #[Test]
    public function itBuildEmptyHeader(): void
    {
        $operation = new Index(shortName: 'my_operation');
        $grid = new Grid(
            name: 'my_grid',
            translationDomain: 'a_domain',
            headerTemplate: 'my_header_template.html.twig',
            sortable: true,
        );
        $property = new Text(
            name: 'my_property',
            label: false,
            sortable: true,
            headerAttributes: ['class' => 'a-class'],
        );

        $headerView = $this->headerViewBuilder->buildHeader(
            operation: $operation,
            grid: $grid,
            property: $property,
            context: [
                'some_context' => 'some_value',
                'sort' => 'name',
                'order' => 'desc',
            ],
        );
        self::assertEquals('my_property', $headerView->name);
    }

    protected function setUp(): void
    {
        $this->headerViewBuilder = new HeaderViewBuilder();
    }
}
