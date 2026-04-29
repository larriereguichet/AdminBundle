<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\EventListener\Resource;

use LAG\AdminBundle\EventListener\Resource\DefineResourceContextListener;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\ResourceInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class DefineResourceContextListenerTest extends TestCase
{
    private DefineResourceContextListener $listener;
    private MockObject $resourceContext;
    private MockObject $resourceFactory;

    #[Test]
    public function itDefinesResourceContext(): void
    {
        $request = new Request();
        $request->attributes->set('_lag_operation', 'admin.book.index');
        $event = new RequestEvent(
            $this->createMock(KernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $operation = $this->createMock(OperationInterface::class);
        $resource = $this->createMock(ResourceInterface::class);
        $resource
            ->expects($this->once())
            ->method('getOperation')
            ->with('index')
            ->willReturn($operation)
        ;

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(false)
        ;
        $this->resourceFactory
            ->expects($this->once())
            ->method('create')
            ->with('admin.book')
            ->willReturn($resource)
        ;
        $this->resourceContext
            ->expects($this->once())
            ->method('setResource')
            ->with($resource)
        ;
        $this->resourceContext
            ->expects($this->once())
            ->method('setOperation')
            ->with($operation)
        ;

        $this->listener->__invoke($event);
    }

    #[Test]
    public function itDoesNothingWhenRequestParameterIsMissing(): void
    {
        $request = new Request();
        $event = new RequestEvent(
            $this->createMock(KernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->resourceContext
            ->expects($this->never())
            ->method('hasOperation')
        ;
        $this->resourceFactory
            ->expects($this->never())
            ->method('create')
        ;

        $this->listener->__invoke($event);
    }

    #[Test]
    public function itDoesNothingWhenContextAlreadyHasOperation(): void
    {
        $request = new Request();
        $request->attributes->set('_lag_operation', 'admin.book.index');
        $event = new RequestEvent(
            $this->createMock(KernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(true)
        ;
        $this->resourceFactory
            ->expects($this->never())
            ->method('create')
        ;

        $this->listener->__invoke($event);
    }

    #[Test]
    public function itDoesNothingWhenParameterHasEmptyResourceName(): void
    {
        // u('.index')->beforeLast('.') returns '' — triggers the empty-name guard
        $request = new Request();
        $request->attributes->set('_lag_operation', '.index');
        $event = new RequestEvent(
            $this->createMock(KernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(false)
        ;
        $this->resourceFactory
            ->expects($this->never())
            ->method('create')
        ;

        $this->listener->__invoke($event);
    }

    #[Test]
    public function itDoesNothingWhenParameterHasEmptyOperationName(): void
    {
        // u('admin.book.')->afterLast('.') returns '' — triggers the empty-name guard
        $request = new Request();
        $request->attributes->set('_lag_operation', 'admin.book.');
        $event = new RequestEvent(
            $this->createMock(KernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(false)
        ;
        $this->resourceFactory
            ->expects($this->never())
            ->method('create')
        ;

        $this->listener->__invoke($event);
    }

    protected function setUp(): void
    {
        $this->resourceContext = $this->createMock(ResourceContextInterface::class);
        $this->resourceFactory = $this->createMock(ResourceFactoryInterface::class);
        $this->listener = new DefineResourceContextListener(
            '_lag_operation',
            $this->resourceContext,
            $this->resourceFactory,
        );
    }
}
