<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Response\Handler;

use LAG\AdminBundle\Exception\Operation\MissingOperationTemplateException;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Response\Handler\TemplateResponseHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class TemplateResponseHandlerTest extends TestCase
{
    private TemplateResponseHandler $handler;
    private MockObject $environment;

    #[Test]
    public function itCreatesATwigResponse(): void
    {
        $resource = new Resource(name: 'my_resource');
        $operation = new Index(template: 'my_template.html.twig')->setResource($resource);
        $data = new \stdClass();
        $request = new Request();

        $this->environment
            ->expects($this->once())
            ->method('render')
            ->with('my_template.html.twig', [
                'resource' => $resource,
                'operation' => $operation,
                'data' => $data,
                'myResources' => $data,
            ])
            ->willReturn('My html content')
        ;

        $response = $this->handler->createResponse($operation, $data);

        self::assertEquals('My html content', $response->getContent());
    }

    #[Test]
    public function itDoesNotCreateAResponseForOperationWithoutTemplate(): void
    {
        $operation = new Index(template: null);
        $data = new \stdClass();
        $request = new Request();

        $this->environment
            ->expects($this->never())
            ->method('render')
        ;

        $this->expectExceptionObject(new MissingOperationTemplateException('The operation "%s" is missing a template', $operation->getFullName()));

        $this->handler->createResponse($operation, $data);
    }

    protected function setUp(): void
    {
        $this->environment = $this->createMock(Environment::class);
        $this->handler = new TemplateResponseHandler($this->environment);
    }
}
