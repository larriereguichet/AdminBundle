<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Response\Handler;

use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Response\Handler\RedirectResponseHandler;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RedirectHandlerTest extends TestCase
{
    private RedirectResponseHandler $handler;
    private MockObject $urlGenerator;

    #[Test]
    public function itCreateARedirectResponseWithRedirectOperation(): void
    {
        $operation = new Create(redirectOperation: 'lag_admin.book.index');
        $data = new \stdClass();

        $this->urlGenerator
            ->expects(self::once())
            ->method('generateFromOperationName')
            ->with('lag_admin.book.index', $data)
            ->willReturn('/index-url')
        ;

        $response = $this->handler->createRedirectResponse($operation, $data);

        self::assertEquals('/index-url', $response->getTargetUrl());
    }

    #[Test]
    public function itCreateARedirectResponseWithRedirectRoute(): void
    {
        $operation = new Create(redirectRoute: 'lag_admin.book.index', redirectRouteParameters: ['id']);
        $data = new \stdClass();

        $this->urlGenerator
            ->expects(self::once())
            ->method('generateFromRouteName')
            ->with('lag_admin.book.index', ['id'], $data)
            ->willReturn('/index-url')
        ;

        $response = $this->handler->createRedirectResponse($operation, $data);

        self::assertEquals('/index-url', $response->getTargetUrl());
    }

    #[Test]
    public function itCreateARedirectResponse(): void
    {
        $operation = new Create();
        $data = new \stdClass();

        $this->urlGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($operation, $data)
            ->willReturn('/same-url')
        ;

        $response = $this->handler->createRedirectResponse($operation, $data);

        self::assertEquals('/same-url', $response->getTargetUrl());
    }

    #[Test]
    public function itCreateARedirectResponseWithResponseCode(): void
    {
        $operation = new Create();
        $data = new \stdClass();

        $this->urlGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($operation, $data)
            ->willReturn('/same-url')
        ;

        $response = $this->handler->createRedirectResponse($operation, $data, ['responseCode' => 301]);

        self::assertEquals('/same-url', $response->getTargetUrl());
        self::assertEquals(301, $response->getStatusCode());
    }

    protected function setUp(): void
    {
        $this->urlGenerator = self::createMock(ResourceUrlGeneratorInterface::class);
        $this->handler = new RedirectResponseHandler($this->urlGenerator);
    }
}
