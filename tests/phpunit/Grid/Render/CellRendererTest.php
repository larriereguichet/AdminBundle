<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\Render;

use LAG\AdminBundle\Grid\Render\CellRenderer;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Resource\Metadata\Text;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Environment;

final class CellRendererTest extends TestCase
{
    private CellRenderer $renderer;
    private Environment $environment;

    #[Test]
    public function itRendersACell(): void
    {
        $cell = new CellView(
            name: 'some_cell',
            template: 'my_template.html.twig',
            options: new Text(),
            context: ['some_context' => 'some_value'],
            data: 'some_data',
            attributes: ['class' => 'some_class'],
        );
        $this->environment
            ->expects(self::once())
            ->method('render')
            ->with($cell->template, [
                'options' => $cell->options,
                'context' => $cell->context,
                'data' => $cell->data,
                'attributes' => new ComponentAttributes($cell->attributes),
            ])
            ->willReturn('<p>some content</p>')
        ;
        $render = $this->renderer->render($cell);

        self::assertEquals('<p>some content</p>', $render);
    }

    #[Test]
    public function itDoesNotRenderAnEmptyCell(): void
    {
        $cell = new CellView(name: 'some_cell');
        $this->environment
            ->expects(self::never())
            ->method('render')
        ;
        $render = $this->renderer->render($cell);

        self::assertEquals('', $render);
    }

    protected function setUp(): void
    {
        $this->environment = self::createMock(Environment::class);
        $this->renderer = new CellRenderer($this->environment);
    }
}
