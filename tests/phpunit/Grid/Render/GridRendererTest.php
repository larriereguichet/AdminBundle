<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\Render;

use LAG\AdminBundle\Grid\Render\GridRenderer;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Resource\Metadata\Index;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class GridRendererTest extends TestCase
{
    private GridRenderer $renderer;
    private MockObject $environment;

    #[Test]
    public function itRendersAGrid(): void
    {
        $grid = new GridView(
            name: 'some_grid',
            type: 'some_type',
            template: 'some_template.html.twig',
            headers: [],
            rows: [],
            options: ['grid_option' => 'grid_value'],
        );
        $operation = new Index();

        $this->environment
            ->expects(self::once())
            ->method('render')
            ->with($grid->template, [
                'grid' => $grid,
                'options' => array_merge_recursive($grid->options, ['an_option' => 'a_value']),
                'operation' => $operation,
                'resource' => $operation->getResource(),
            ])
            ->willReturn('<span>some content</span>')
        ;

        $render = $this->renderer->render($grid, $operation, ['an_option' => 'a_value']);

        self::assertEquals('<span>some content</span>', $render);
    }

    protected function setUp(): void
    {
        $this->environment = self::createMock(Environment::class);
        $this->renderer = new GridRenderer($this->environment);
    }
}
