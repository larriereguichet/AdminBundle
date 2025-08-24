<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\View;

use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\View\Render\LinkRenderer;
use LAG\AdminBundle\View\Render\LinkRendererInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

final class LinkRendererTest extends TestCase
{
    private LinkRendererInterface $linkRenderer;
    private MockObject $urlGenerator;
    private MockObject $validator;
    private MockObject $environment;

    #[Test]
    public function itRendersLink(): void
    {
        $link = new Link(
            template: 'some_template.html.twig',
        );

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($link, [new Valid()])
            ->willReturn(new ConstraintViolationList())
        ;
        $this->urlGenerator
            ->expects($this->once())
            ->method('generateFromUrl')
            ->with($link)
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
        $this->urlGenerator = $this->createMock(ResourceUrlGeneratorInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->environment = $this->createMock(Environment::class);
        $this->linkRenderer = new LinkRenderer(
            $this->urlGenerator,
            $this->validator,
            $this->environment,
        );
    }
}
