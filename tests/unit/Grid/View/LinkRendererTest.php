<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\View;

use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Routing\UrlGenerator\LinkUrlGeneratorInterface;
use LAG\AdminBundle\Tests\Unit\TestCase;
use LAG\AdminBundle\View\Render\LinkRenderer;
use LAG\AdminBundle\View\Render\LinkRendererInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Environment;

final class LinkRendererTest extends TestCase
{
    private LinkRendererInterface $linkRenderer;
    private MockObject $urlGenerator;
    private MockObject $environment;

    #[Test]
    public function itRendersLink(): void
    {
        $link = new Link(
            template: 'some_template.html.twig',
        );

        $this->urlGenerator
            ->expects($this->once())
            ->method('generateUrl')
            ->with($link, null)
            ->willReturn('/some/url')
        ;
        $this->environment
            ->expects($this->once())
            ->method('render')
            ->with('some_template.html.twig', [
                'link' => $link->withUrl('/some/url'),
                'options' => [],
            ])
            ->willReturn('some content')
        ;

        $render = $this->linkRenderer->render($link);

        self::assertEquals('some content', $render);
    }

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(LinkUrlGeneratorInterface::class);
        $this->environment = $this->createMock(Environment::class);
        $this->linkRenderer = new LinkRenderer(
            $this->urlGenerator,
            $this->environment,
        );
    }
}
