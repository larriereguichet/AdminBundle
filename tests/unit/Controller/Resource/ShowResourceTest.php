<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Resource;

use LAG\AdminBundle\Controller\Resource\ShowResource;
use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\Entity\FakeEntity;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShowResourceTest extends TestCase
{
    private ShowResource $controller;
    private MockObject $contextBuilder;
    private MockObject $provider;
    private MockObject $responseHandler;
    private MockObject $eventDispatcher;

    #[Test]
    public function itShowResourceForm(): void
    {
        $operation = new Show();
        $request = new Request();
        $data = new FakeEntity();
        $data->id = 666;

        $this->contextBuilder
            ->expects($this->once())
            ->method('buildContext')
            ->with($operation, $request)
        ;
        $this->provider
            ->expects($this->once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchEvents')
            ->with(new ResourceControllerEvent($operation, $request, $data), ResourceControllerEvents::RESOURCE_CONTROLLER)
        ;
        $this->responseHandler
            ->expects($this->once())
            ->method('createResponse')
            ->with($operation, $data)
            ->willReturn(new Response(content: 'some html'))
        ;
        $response = $this->controller->__invoke($operation, $request);

        self::assertEquals('some html', $response->getContent());
    }

    #[Test]
    public function itReturnsAResponseFromEvent(): void
    {
        $operation = new Show();
        $request = new Request();
        $data = new FakeEntity();
        $data->id = 666;

        $this->provider
            ->expects($this->once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchEvents')
            ->willReturnCallback(function (
                ResourceControllerEvent $event,
                string $expectedEventPattern,
            ): void {
                self::assertEquals(ResourceControllerEvents::RESOURCE_CONTROLLER, $expectedEventPattern);
                $event->setResponse(new Response(content: 'some event html'));
            })
        ;
        $this->responseHandler
            ->expects($this->never())
            ->method('createResponse')
        ;
        $response = $this->controller->__invoke($operation, $request);

        self::assertEquals('some event html', $response->getContent());
    }

    protected function setUp(): void
    {
        $this->contextBuilder = $this->createMock(ContextBuilderInterface::class);
        $this->provider = $this->createMock(ProviderInterface::class);
        $this->eventDispatcher = $this->createMock(ResourceEventDispatcherInterface::class);
        $this->responseHandler = $this->createMock(ResponseHandlerInterface::class);
        $this->controller = new ShowResource(
            $this->contextBuilder,
            $this->provider,
            $this->eventDispatcher,
            $this->responseHandler,
        );
    }
}
