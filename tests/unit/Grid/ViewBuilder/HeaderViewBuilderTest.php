<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\ViewBuilder\AttributeBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\HeaderBuilder;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Text;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Runtime\EscaperRuntime;

final class HeaderViewBuilderTest extends TestCase
{
    private HeaderBuilder $headerViewBuilder;
    private MockObject $attributeBuilder;

    #[Test]
    public function itBuildAHeader(): void
    {
        $operation = new Index(name: 'my_operation');
        $grid = new Grid(
            name: 'my_grid',
            sortable: true,
        );
        $property = new Text(
            name: 'my_property',
            label: 'Some property',
            sortable: true,
            headerAttributes: ['class' => 'a-class'],
        );
        $attributes = new ComponentAttributes(['class' => 'a-class'], new EscaperRuntime());

        $this->attributeBuilder
            ->expects($this->once())
            ->method('buildAttributes')
            ->with(['class' => 'a-class'])
            ->willReturn($attributes)
        ;

        $headerView = $this->headerViewBuilder->buildHeader(
            operation: $operation,
            grid: $grid,
            property: $property,
            context: [
                'some_context' => 'some_value',
                'sort' => 'name',
                'order' => 'desc',
                'translation_domain' => 'a_domain',
            ],
        );

        self::assertEquals('my_property', $headerView->name);
        self::assertEquals('Some property', $headerView->label);
        self::assertEquals('a_domain', $headerView->translationDomain);
        self::assertEquals('desc', $headerView->order);
        self::assertTrue($headerView->sortable);
        self::assertEquals($attributes, $headerView->attributes);
    }

    #[Test]
    public function itBuildEmptyHeader(): void
    {
        $operation = new Index(name: 'my_operation');
        $grid = new Grid(
            name: 'my_grid',
            sortable: true,
        );
        $property = new Text(
            name: 'my_property',
            label: false,
            sortable: true,
            headerAttributes: [],
        );
        $attributes = new ComponentAttributes([], new EscaperRuntime());

        $this->attributeBuilder
            ->expects($this->once())
            ->method('buildAttributes')
            ->willReturn($attributes)
        ;

        $headerView = $this->headerViewBuilder->buildHeader(
            operation: $operation,
            grid: $grid,
            property: $property,
            context: ['sort' => 'name', 'order' => 'desc'],
        );

        self::assertEquals('my_property', $headerView->name);
        self::assertNull($headerView->label);
    }

    protected function setUp(): void
    {
        $this->attributeBuilder = $this->createMock(AttributeBuilderInterface::class);
        $this->headerViewBuilder = new HeaderBuilder($this->attributeBuilder);
    }
}
