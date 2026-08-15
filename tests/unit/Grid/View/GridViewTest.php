<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\View;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\View\RowView;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Runtime\EscaperRuntime;

final class GridViewTest extends TestCase
{
    #[Test]
    public function itIteratesOverRows(): void
    {
        $attributes = new ComponentAttributes([], new EscaperRuntime());
        $row1 = $this->createStub(RowView::class);
        $row2 = $this->createStub(RowView::class);

        $grid = new GridView(
            name: 'my_grid',
            type: 'table',
            rows: [$row1, $row2],
            attributes: $attributes,
        );

        $rows = iterator_to_array($grid->getIterator());

        self::assertCount(2, $rows);
        self::assertSame($row1, $rows[0]);
        self::assertSame($row2, $rows[1]);
    }
}
