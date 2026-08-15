<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Response\Handler;

use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Response\Handler\ContentResponseHandlerInterface;
use LAG\AdminBundle\Response\Handler\JsonResponseHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
        $data = new \stdClass();
        $request = new Request(server: ['CONTENT_TYPE' => 'application/json']);

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($data, 'json', ['groups' => 'my-group'])
            ->willReturn('{"json": "content"}')
        ;
        $this->responseHandler
            ->expects($this->never())
            ->method('createResponse')
        ;

        $response = $this->handler->createResponse($request, $operation, $data);

        self::assertEquals('{"json": "content"}', $response->getContent());
    }

    #[Test]
    public function itDoesNotHandleHtmlResponse(): void
    {
        $operation = new Update();
        $data = new \stdClass();
        $request = new Request(server: ['Content-Type' => 'text/html']);

        $this->serializer
            ->expects($this->never())
            ->method('serialize')
        ;
        $this->responseHandler
            ->expects($this->once())
            ->method('createResponse')
            ->with($request, $operation, $data)
            ->willReturn(new Response('some content'))
        ;

        $response = $this->handler->createResponse($request, $operation, $data);

        self::assertEquals('some content', $response->getContent());
    }

    protected function setUp(): void
    {
        $this->responseHandler = $this->createMock(ContentResponseHandlerInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->handler = new JsonResponseHandler(
            $this->responseHandler,
            $this->serializer,
        );
    }
}
