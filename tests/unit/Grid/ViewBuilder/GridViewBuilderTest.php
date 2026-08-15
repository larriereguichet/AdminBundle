<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\RowView;
use LAG\AdminBundle\Grid\ViewBuilder\AttributeBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\GridBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\RowBuilderInterface;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Tests\Unit\Fixtures\Book;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Runtime\EscaperRuntime;

final class GridViewBuilderTest extends TestCase
{
    private GridBuilder $gridViewBuilder;
    private MockObject $rowBuilder;
    private MockObject $attributeBuilder;

    #[Test]
    public function itBuildsAGridView(): void
    {
        $grid = new Grid(
            name: 'my_grid',
            type: 'some_type',
        );
        $resource = new Resource();
        $operation = new Index()->setResource($resource);
        $book1 = new Book();
        $book2 = new Book();
        $data = [$book1, $book2];
        $context = ['some_key' => 'some_value'];

        $headerRow = $this->createStub(RowView::class);

        $this->rowBuilder
            ->expects($this->once())
            ->method('buildHeadersRow')
            ->with($operation, $grid, $context)
            ->willReturn($headerRow)
        ;
        $this->rowBuilder
            ->expects($this->exactly(2))
            ->method('buildRow')
        ;
        $this->attributeBuilder
            ->expects($this->atLeastOnce())
            ->method('buildAttributes')
            ->willReturn(new ComponentAttributes([], new EscaperRuntime()))
        ;

        $gridView = $this->gridViewBuilder->build($grid, $operation, $data, $context);

        $this->assertEquals($grid->getName(), $gridView->name);
        $this->assertEquals($grid->getType(), $gridView->type);
    }

    #[Test]
    public function itBuildsAGridViewWithoutHeaders(): void
    {
        $grid = new Grid(name: 'my_grid', type: 'table', useHeaders: false);
        $resource = new Resource();
        $operation = new Index()->setResource($resource);

        $this->rowBuilder->expects($this->never())->method('buildHeadersRow');
        $this->attributeBuilder->method('buildAttributes')->willReturn(new ComponentAttributes([], new EscaperRuntime()));

        $gridView = $this->gridViewBuilder->build($grid, $operation, [], []);

        self::assertNull($gridView->headers);
    }

    #[Test]
    public function itBuildsAGridViewWithTitle(): void
    {
        $grid = new Grid(name: 'my_grid', type: 'table', title: 'My Books');
        $resource = new Resource(translationDomain: 'messages');
        $operation = new Index()->setResource($resource);

        $this->attributeBuilder->method('buildAttributes')->willReturn(new ComponentAttributes([], new EscaperRuntime()));
        $this->rowBuilder->method('buildHeadersRow')->willReturn($this->createStub(RowView::class));

        $gridView = $this->gridViewBuilder->build($grid, $operation, [], []);

        self::assertNotNull($gridView->title);
        self::assertSame('My Books', $gridView->title->title);
    }

    #[Test]
    public function itBuildsAGridViewWithNoTitle(): void
    {
        $grid = new Grid(name: 'my_grid', type: 'table', title: false);
        $resource = new Resource();
        $operation = new Index()->setResource($resource);

        $this->attributeBuilder->method('buildAttributes')->willReturn(new ComponentAttributes([], new EscaperRuntime()));
        $this->rowBuilder->method('buildHeadersRow')->willReturn($this->createStub(RowView::class));

        $gridView = $this->gridViewBuilder->build($grid, $operation, [], []);

        self::assertNull($gridView->title);
    }

    protected function setUp(): void
    {
        $this->rowBuilder = $this->createMock(RowBuilderInterface::class);
        $this->attributeBuilder = $this->createMock(AttributeBuilderInterface::class);
        $this->gridViewBuilder = new GridBuilder(
            $this->rowBuilder,
            $this->attributeBuilder,
        );
    }
}
