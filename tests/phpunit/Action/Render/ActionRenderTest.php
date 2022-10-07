<?php

namespace LAG\AdminBundle\Tests\Action\Render;

use LAG\AdminBundle\Exception\Validation\InvalidActionException;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Twig\Render\ActionRenderer;
use LAG\AdminBundle\Twig\Render\ActionRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

class ActionRenderTest extends TestCase
{
    private ActionRendererInterface $actionRenderer;
    private MockObject $urlGenerator;
    private MockObject $validator;
    private MockObject $environment;

    public function testService()
    {
        $this->assertServiceExists(ActionRenderer::class);
        $this->assertServiceExists(ActionRendererInterface::class);
    }

    public function testRenderWithRouteName(): void
    {
        $action = new Action(
            routeName: 'my_route',
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
        $action = new Action(
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
        $action = new Action();
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
        $this->actionRenderer = new ActionRenderer(
            $this->urlGenerator,
            $this->validator,
            $this->environment,
        );
    }
}
