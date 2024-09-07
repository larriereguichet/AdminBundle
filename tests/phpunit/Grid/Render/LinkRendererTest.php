<?php

namespace LAG\AdminBundle\Tests\Grid\Render;

use LAG\AdminBundle\Exception\InvalidLinkException;
use LAG\AdminBundle\Grid\Render\LinkRenderer;
use LAG\AdminBundle\Resource\Metadata\Link;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
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

        $violationList = self::createMock(ConstraintViolationListInterface::class);
        $violationList
            ->expects(self::once())
            ->method('count')
            ->willReturn(0)
        ;
        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->willReturn($violationList)
        ;
        $this->environment
            ->expects(self::once())
            ->method('render')
        ;

        $this->renderer->render($link);
    }

    #[Test]
    public function itDoesNotRenderInvalidLink(): void
    {
        $link = new Link();

        $violationList = self::createMock(ConstraintViolationListInterface::class);
        $violationList
            ->expects(self::once())
            ->method('count')
            ->willReturn(1)
        ;
        $this->validator
            ->expects(self::once())
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
        $this->environment = self::createMock(Environment::class);
        $this->urlGenerator = self::createMock(UrlGeneratorInterface::class);
        $this->validator = self::createMock(ValidatorInterface::class);
        $this->renderer = new LinkRenderer(
            $this->urlGenerator,
            $this->validator,
            $this->environment,
        );
    }
}
