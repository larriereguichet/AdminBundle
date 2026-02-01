<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\Row;
use LAG\AdminBundle\Grid\ViewBuilder\ActionBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\AttributeBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\RowViewBuilderInterface;
use LAG\AdminBundle\Metadata\Attribute\Action;
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
    private GridViewBuilder $gridViewBuilder;
    private MockObject $rowBuilder;
    private MockObject $actionBuilder;
    private MockObject $attributeBuilder;

    #[Test]
    public function itBuildsAGridView(): void
    {
        $action = new Action(name: 'my_action');
        $grid = new Grid(
            name: 'my_grid',
            type: 'some_type',
            collectionActions: [$action],
        );
        $operation = new Index()->setResource(new Resource());
        $book1 = new Book();
        $book2 = new Book();
        $data = [$book1, $book2];
        $context = ['some_key' => 'some_value'];

        $headerRow = $this->createMock(Row::class);

        $this->rowBuilder
            ->expects($this->once())
            ->method('buildHeadersRow')
            ->with($operation, $grid, $context)
            ->willReturn($headerRow)
        ;
        $this->rowBuilder
            ->expects($this->exactly(2))
            ->method('buildRow')
            ->with($operation, $grid, $book1, $context)
        ;
        $this->actionBuilder
            ->expects($this->once())
            ->method('buildAction')
            ->with($action, $data, $context)
        ;
        $this->attributeBuilder
            ->expects($this->once())
            ->method('buildAttributes')
            ->willReturn(new ComponentAttributes([], new EscaperRuntime()))
        ;
        $gridView = $this->gridViewBuilder->build($grid, $operation, $data, $context);

        $this->assertEquals($grid->getName(), $gridView->name);
        $this->assertEquals($grid->getType(), $gridView->type);
    }

    protected function setUp(): void
    {
        $this->rowBuilder = $this->createMock(RowViewBuilderInterface::class);
        $this->actionBuilder = $this->createMock(ActionBuilderInterface::class);
        $this->attributeBuilder = $this->createMock(AttributeBuilderInterface::class);
        $this->gridViewBuilder = new GridViewBuilder(
            $this->rowBuilder,
            $this->actionBuilder,
            $this->attributeBuilder,
        );
    }
}
