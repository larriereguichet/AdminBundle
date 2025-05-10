<?php

namespace LAG\AdminBundle\Tests\Response\Handler;

use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\Response\Handler\JsonResponseHandler;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class JsonResponseHandlerTest extends TestCase
{
    private JsonResponseHandler $handler;
    private MockObject $responseHandler;
    private MockObject $serializer;

    #[Test]
    public function itHandlesJsonResponses(): void
    {
        $operation = new Update(normalizationContext: ['groups' => 'my-group']);
        $data = new stdClass();
        $request = new Request(server: ['CONTENT_TYPE' => 'application/json']);

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

        $response = $this->handler->createResponse($operation, $data, $request);

        self::assertEquals('{"json": "content"}', $response->getContent());
    }

    #[Test]
    public function itDoesNotHandleHtmlResponse(): void
    {
        $operation = new Update();
        $data = new stdClass();
        $request = new Request(server: ['Content-Type' => 'text/html']);

        $this->serializer
            ->expects(self::never())
            ->method('serialize')
        ;
        $this->responseHandler
            ->expects(self::once())
            ->method('createResponse')
            ->with($operation, $data, $request)
            ->willReturn(new Response('some content'))
        ;

        $response = $this->handler->createResponse($operation, $data, $request);

        self::assertEquals('some content', $response->getContent());
    }

    protected function setUp(): void
    {
        $this->responseHandler = self::createMock(ResponseHandlerInterface::class);
        $this->serializer = self::createMock(SerializerInterface::class);
        $this->handler = new JsonResponseHandler(
            $this->responseHandler,
            $this->serializer,
        );
    }
}
