<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\EventListener\Resource;

use LAG\AdminBundle\EventListener\Resource\InitializeResourceContextListener;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\ResourceInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class DefineResourceContextListenerTest extends TestCase
{
    private InitializeResourceContextListener $listener;
    private MockObject $resourceContext;
    private MockObject $resourceFactory;

    #[Test]
    public function itPushesAndDefinesResourceContext(): void
    {
        $request = new Request();
        $request->attributes->set('_lag_operation', 'admin.book.index');
        $event = new RequestEvent(
            $this->createStub(KernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $operation = $this->createStub(OperationInterface::class);
        $resource = $this->createMock(ResourceInterface::class);
        $resource
            ->expects($this->once())
            ->method('getOperation')
            ->with('index')
            ->willReturn($operation)
        ;

        $this->resourceContext->expects($this->once())->method('push');
        $this->resourceFactory
            ->expects($this->once())
            ->method('create')
            ->with('admin.book')
            ->willReturn($resource)
        ;
        $this->resourceContext->expects($this->once())->method('setResource')->with($resource);
        $this->resourceContext->expects($this->once())->method('setOperation')->with($operation);

        $this->listener->onRequest($event);
    }

    #[Test]
    public function itPushesButDoesNothingWhenRequestParameterIsMissing(): void
    {
        $request = new Request();
        $event = new RequestEvent(
            $this->createStub(KernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->resourceContext->expects($this->once())->method('push');
        $this->resourceFactory->expects($this->never())->method('create');
        $this->resourceContext->expects($this->never())->method('setResource');
        $this->resourceContext->expects($this->never())->method('setOperation');

        $this->listener->onRequest($event);
    }

    #[Test]
    public function itPushesButDoesNothingWhenParameterHasEmptyResourceName(): void
    {
        $request = new Request();
        $request->attributes->set('_lag_operation', '.index');
        $event = new RequestEvent(
            $this->createStub(KernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->resourceContext->expects($this->once())->method('push');
        $this->resourceFactory->expects($this->never())->method('create');

        $this->listener->onRequest($event);
    }

    #[Test]
    public function itPushesButDoesNothingWhenParameterHasEmptyOperationName(): void
    {
        $request = new Request();
        $request->attributes->set('_lag_operation', 'admin.book.');
        $event = new RequestEvent(
            $this->createStub(KernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->resourceContext->expects($this->once())->method('push');
        $this->resourceFactory->expects($this->never())->method('create');

        $this->listener->onRequest($event);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function itPopsContextOnFinishRequest(): void
    {
        $request = new Request();
        $request->attributes->set('_lag_resource_context_pushed', true);

        $event = new FinishRequestEvent(
            $this->createStub(KernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->resourceContext->expects($this->once())->method('pop');

        $this->listener->onFinishRequest($event);
    }

    #[Test]
    public function itDoesNotPopContextWhenItWasNeverPushed(): void
    {
        // An earlier kernel.request listener may short-circuit the request, so onRequest never ran. Popping here
        // would drop the frame of the parent request
        $event = new FinishRequestEvent(
            $this->createStub(KernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->resourceContext->expects($this->never())->method('pop');

        $this->listener->onFinishRequest($event);
    }

    protected function setUp(): void
    {
        $this->resourceContext = $this->createMock(ResourceContextInterface::class);
        $this->resourceFactory = $this->createMock(ResourceFactoryInterface::class);
        $this->listener = new InitializeResourceContextListener(
            '_lag_operation',
            $this->resourceContext,
            $this->resourceFactory,
        );
    }
}
