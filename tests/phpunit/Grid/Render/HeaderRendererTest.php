<?php

namespace LAG\AdminBundle\Tests\Grid\Render;

use LAG\AdminBundle\Grid\Render\HeaderRenderer;
use LAG\AdminBundle\Grid\View\HeaderView;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Environment;

final class HeaderRendererTest extends TestCase
{
    private HeaderRenderer $renderer;
    private MockObject $environment;

    #[Test]
    public function itRendersHeader(): void
    {
        $header = new HeaderView(
            name: 'my_header',
            template: 'my_template.html.twig',
        );

        $this->environment
            ->expects(self::once())
            ->method('render')
            ->with($header->template, [
                'header' => $header,
                'options' => ['attributes' => ['id' => 'header-id']],
                'attributes' => new ComponentAttributes(['id' => 'header-id']),
            ])
            ->willReturn('<span>some content</span>')
        ;

        $render = $this->renderer->render($header, ['attributes' => ['id' => 'header-id']]);
        self::assertEquals('<span>some content</span>', $render);
    }

    #[Test]
    public function itDoesNotRenderEmptyHeaders(): void
    {
        $header = new HeaderView(
            name: 'my_header',
            template: null,
        );

        $this->environment
            ->expects(self::never())
            ->method('render')
        ;

        $this->renderer->render($header);
    }

    protected function setUp(): void
    {
        $this->environment = self::createMock(Environment::class);
        $this->renderer = new HeaderRenderer($this->environment);
    }
}
