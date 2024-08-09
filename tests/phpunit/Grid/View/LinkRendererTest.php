<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\View;

use LAG\AdminBundle\Resource\Metadata\Link;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
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
            ->expects(self::once())
            ->method('validate')
            ->with($link, [new Valid()])
            ->willReturn(new ConstraintViolationList())
        ;
        $this->urlGenerator
            ->expects(self::once())
            ->method('generateUrl')
            ->with($link)
            ->willReturn('/some/url')
        ;
        $this->environment
            ->expects(self::once())
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
        $this->urlGenerator = self::createMock(UrlGeneratorInterface::class);
        $this->validator = self::createMock(ValidatorInterface::class);
        $this->environment = self::createMock(Environment::class);
        $this->linkRenderer = new LinkRenderer(
            $this->urlGenerator,
            $this->validator,
            $this->environment,
        );
    }
}
