<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\ViewBuilder\HeaderViewBuilder;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Text;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Runtime\EscaperRuntime;

final class HeaderViewBuilderTest extends TestCase
{
    private HeaderViewBuilder $headerViewBuilder;
    private MockObject $environment;

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
        $this->environment
            ->expects(self::once())
            ->method('getRuntime')
            ->with(EscaperRuntime::class)
            ->willReturn(new EscaperRuntime())
        ;

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
        self::assertEquals(['class' => 'a-class'], $headerView->attributes->all());
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
        $this->environment
            ->expects(self::once())
            ->method('getRuntime')
            ->with(EscaperRuntime::class)
            ->willReturn(new EscaperRuntime())
        ;

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
        $this->environment = self::createMock(Environment::class);
        $this->headerViewBuilder = new HeaderViewBuilder($this->environment);
    }
}
