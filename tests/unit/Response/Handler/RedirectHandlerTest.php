<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Response\Handler;

use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Response\Handler\RedirectResponseHandler;
use LAG\AdminBundle\Routing\UrlGenerator\OperationUrlGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class RedirectHandlerTest extends TestCase
{
    private RedirectResponseHandler $handler;
    private MockObject $operationFactory;
    private MockObject $operationUrlGenerator;
    private MockObject $urlGenerator;

    #[Test]
    public function itCreateARedirectResponseWithRedirectOperation(): void
    {
        $operation = new Create(redirectOperation: 'lag_admin.book.index');
        $targetOperation = new Show();
        $data = new \stdClass();
        $request = new Request();

        $this->urlGenerator
            ->expects($this->never())
            ->method('generateUrl')
        ;
        $this->operationFactory
            ->expects($this->once())
            ->method('create')
            ->with('lag_admin.book.index')
            ->willReturn($targetOperation)
        ;
        $this->operationUrlGenerator
            ->expects($this->once())
            ->method('generateUrl')
            ->with($targetOperation)
            ->willReturn('/index-url')
        ;

        $response = $this->handler->createRedirectResponse($request, $operation, $data);

        self::assertEquals('/index-url', $response->getTargetUrl());
    }

    #[Test]
    public function itCreateARedirectResponseWithRedirectRoute(): void
    {
        $operation = new Create(redirectRoute: 'lag_admin.book.index', redirectRouteParameters: ['id']);
        $data = new \stdClass();
        $request = new Request();

        $this->operationFactory
            ->expects($this->never())
            ->method('create')
        ;
        $this->operationUrlGenerator
            ->expects($this->never())
            ->method('generateUrl')
        ;
        $this->urlGenerator
            ->expects($this->once())
            ->method('generateUrl')
            ->with('lag_admin.book.index', ['id'], $data)
            ->willReturn('/index-url')
        ;

        $response = $this->handler->createRedirectResponse($request, $operation, $data);

        self::assertEquals('/index-url', $response->getTargetUrl());
    }

    #[Test]
    public function itCreateARedirectResponse(): void
    {
        $operation = new Create();
        $data = new \stdClass();
        $request = new Request();

        $this->operationFactory
            ->expects($this->never())
            ->method('create')
        ;
        $this->urlGenerator
            ->expects($this->never())
            ->method('generateUrl')
        ;
        $this->operationUrlGenerator
            ->expects($this->once())
            ->method('generateUrl')
            ->with($operation, $data)
            ->willReturn('/same-url')
        ;

        $response = $this->handler->createRedirectResponse($request, $operation, $data);

        self::assertEquals('/same-url', $response->getTargetUrl());
    }

    #[Test]
    public function itCreateARedirectResponseWithResponseCode(): void
    {
        $operation = new Create();
        $data = new \stdClass();
        $request = new Request();

        $this->operationFactory
            ->expects($this->never())
            ->method('create')
        ;
        $this->urlGenerator
            ->expects($this->never())
            ->method('generateUrl')
        ;
        $this->operationUrlGenerator
            ->expects($this->once())
            ->method('generateUrl')
            ->with($operation, $data)
            ->willReturn('/same-url')
        ;

        $response = $this->handler->createRedirectResponse($request, $operation, $data);

        self::assertEquals('/same-url', $response->getTargetUrl());
        self::assertEquals(302, $response->getStatusCode());
    }

    protected function setUp(): void
    {
        $this->operationFactory = $this->createMock(OperationFactoryInterface::class);
        $this->operationUrlGenerator = $this->createMock(OperationUrlGeneratorInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->handler = new RedirectResponseHandler(
            $this->operationFactory,
            $this->operationUrlGenerator,
            $this->urlGenerator,
        );
    }
}
