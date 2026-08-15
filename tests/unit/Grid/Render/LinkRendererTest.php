<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\Render;

use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Routing\UrlGenerator\LinkUrlGeneratorInterface;
use LAG\AdminBundle\View\Render\LinkRenderer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class LinkRendererTest extends TestCase
{
    private LinkRenderer $renderer;
    private MockObject $urlGenerator;
    private MockObject $environment;

    #[Test]
    public function itRendersLink(): void
    {
        $link = new Link(template: 'some_template.html.twig');

        $this->urlGenerator
            ->expects($this->once())
            ->method('generateUrl')
            ->with($link, null)
            ->willReturn('/some-url')
        ;
        $this->environment
            ->expects($this->once())
            ->method('render')
            ->with('some_template.html.twig')
            ->willReturn('<a href="/some-url">link</a>')
        ;

        $result = $this->renderer->render($link);

        self::assertEquals('<a href="/some-url">link</a>', $result);
    }

    #[Test]
    public function itDoesNotRenderInvalidLink(): void
    {
        $link = new Link(template: 'some_template.html.twig');
        $data = new \stdClass();

        $this->urlGenerator
            ->expects($this->once())
            ->method('generateUrl')
            ->with($link, $data)
            ->willReturn('/some-url')
        ;
        $this->environment
            ->expects($this->once())
            ->method('render')
            ->willReturn('')
        ;

        $result = $this->renderer->render($link, $data);

        self::assertEquals('', $result);
    }

    protected function setUp(): void
    {
        $this->environment = $this->createMock(Environment::class);
        $this->urlGenerator = $this->createMock(LinkUrlGeneratorInterface::class);
        $this->renderer = new LinkRenderer(
            $this->urlGenerator,
            $this->environment,
        );
    }
}
