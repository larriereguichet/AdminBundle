<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\Render;

use LAG\AdminBundle\Exception\InvalidLinkException;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;
use LAG\AdminBundle\View\Render\LinkRenderer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

final class LinkRendererTest extends TestCase
{
    private LinkRenderer $renderer;
    private MockObject $urlGenerator;
    private MockObject $validator;
    private MockObject $environment;

    #[Test]
    public function itRendersLink(): void
    {
        $link = new Link();

        $violationList = $this->createMock(ConstraintViolationListInterface::class);
        $violationList
            ->expects($this->once())
            ->method('count')
            ->willReturn(0)
        ;
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn($violationList)
        ;
        $this->environment
            ->expects($this->once())
            ->method('render')
        ;

        $this->renderer->render($link);
    }

    #[Test]
    public function itDoesNotRenderInvalidLink(): void
    {
        $link = new Link();

        $violationList = $this->createMock(ConstraintViolationListInterface::class);
        $violationList
            ->expects($this->once())
            ->method('count')
            ->willReturn(1)
        ;
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn($violationList)
        ;
        $this->environment
            ->expects(self::never())
            ->method('render')
        ;
        self::expectException(InvalidLinkException::class);

        $this->renderer->render($link);
    }

    protected function setUp(): void
    {
        $this->environment = $this->createMock(Environment::class);
        $this->urlGenerator = $this->createMock(ResourceUrlGeneratorInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->renderer = new LinkRenderer(
            $this->urlGenerator,
            $this->validator,
            $this->environment,
        );
    }
}
