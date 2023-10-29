<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\View;

use LAG\AdminBundle\Exception\Validation\InvalidActionException;
use LAG\AdminBundle\Grid\View\LinkRenderer;
use LAG\AdminBundle\Grid\View\LinkRendererInterface;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

class LinkRendererTest extends TestCase
{
    private LinkRendererInterface $actionRenderer;
    private MockObject $urlGenerator;
    private MockObject $validator;
    private MockObject $environment;

    public function testService(): void
    {
        $this->assertServiceExists(LinkRenderer::class);
        $this->assertServiceExists(LinkRendererInterface::class);
    }

    public function testRenderWithRouteName(): void
    {
        $action = new Link(
            route: 'my_route',
            routeParameters: ['id' => []],
            template: 'my_template.html.twig',
            resourceName: 'my_resource', // should not be used if route is provided
        );
        $data = new \stdClass();

        $this
            ->validator
            ->expects($this->once())
            ->method('validate')
            ->with($action, [new Valid()])
            ->willReturn($this->createMock(ConstraintViolationListInterface::class))
        ;

        $this
            ->urlGenerator
            ->expects($this->once())
            ->method('generateFromRouteName')
            ->with('my_route', ['id' => []], $data)
            ->willReturn('/my-url')
        ;

        $this
            ->environment
            ->expects($this->once())
            ->method('render')
            ->with('my_template.html.twig')
            ->willReturn('<render></render>')
        ;

        $render = $this->actionRenderer->render($action, $data);

        $this->assertEquals('<render></render>', $render);
    }

    public function testRenderWithResourceName(): void
    {
        $action = new Link(
            template: 'my_template.html.twig',
            resourceName: 'my_resource',
            operationName: 'my_operation',
        );
        $data = new \stdClass();

        $this
            ->validator
            ->expects($this->once())
            ->method('validate')
            ->with($action, [new Valid()])
            ->willReturn($this->createMock(ConstraintViolationListInterface::class))
        ;

        $this
            ->urlGenerator
            ->expects($this->once())
            ->method('generateFromOperationName')
            ->with('my_resource', 'my_operation', $data)
            ->willReturn('/my-url')
        ;

        $this
            ->environment
            ->expects($this->once())
            ->method('render')
            ->with('my_template.html.twig')
            ->willReturn('<render></render>')
        ;

        $render = $this->actionRenderer->render($action, $data);

        $this->assertEquals('<render></render>', $render);
    }

    public function testInvalidAction(): void
    {
        $action = new Link();
        $data = new \stdClass();

        $violations = $this->createMock(ConstraintViolationListInterface::class);

        $this
            ->validator
            ->expects($this->once())
            ->method('validate')
            ->with($action, [new Valid()])
            ->willReturn($violations)
        ;
        $violations
            ->expects($this->once())
            ->method('count')
            ->willReturn(1)
        ;

        $this->expectException(InvalidActionException::class);
        $this->actionRenderer->render($action, $data);
    }

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->environment = $this->createMock(Environment::class);
        $this->actionRenderer = new LinkRenderer(
            $this->urlGenerator,
            $this->validator,
            $this->environment,
        );
    }
}
