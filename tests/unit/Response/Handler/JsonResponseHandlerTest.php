<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Response\Handler;

use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\Response\Handler\JsonResponseHandler;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class JsonResponseHandlerTest extends TestCase
{
    private JsonResponseHandler $handler;
    private MockObject $requestStack;
    private MockObject $responseHandler;
    private MockObject $serializer;

    #[Test]
    public function itHandlesJsonResponses(): void
    {
        $operation = new Update(normalizationContext: ['groups' => 'my-group']);
        $data = new \stdClass();
        $request = new Request(server: ['CONTENT_TYPE' => 'application/json']);

        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->serializer
            ->expects(self::once())
            ->method('serialize')
            ->with($data, 'json', ['groups' => 'my-group'])
            ->willReturn('{"json": "content"}')
        ;
        $this->responseHandler
            ->expects(self::never())
            ->method('createResponse')
        ;

        $response = $this->handler->createResponse($operation, $data);

        self::assertEquals('{"json": "content"}', $response->getContent());
    }

    #[Test]
    public function itDoesNotHandleHtmlResponse(): void
    {
        $operation = new Update();
        $data = new \stdClass();
        $request = new Request(server: ['Content-Type' => 'text/html']);

        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->serializer
            ->expects(self::never())
            ->method('serialize')
        ;
        $this->responseHandler
            ->expects(self::once())
            ->method('createResponse')
            ->with($operation, $data)
            ->willReturn(new Response('some content'))
        ;

        $response = $this->handler->createResponse($operation, $data);

        self::assertEquals('some content', $response->getContent());
    }

    protected function setUp(): void
    {
        $this->requestStack = self::createMock(RequestStack::class);
        $this->responseHandler = self::createMock(ResponseHandlerInterface::class);
        $this->serializer = self::createMock(SerializerInterface::class);
        $this->handler = new JsonResponseHandler(
            $this->requestStack,
            $this->responseHandler,
            $this->serializer,
        );
    }
}
