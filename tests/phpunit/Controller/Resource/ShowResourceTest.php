<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Resource;

use LAG\AdminBundle\Controller\Resource\ShowResource;
use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Metadata\Show;
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

        $this->provider
            ->expects(self::once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->eventDispatcher
            ->expects(self::once())
            ->method('dispatchEvents')
            ->with(new ResourceControllerEvent($operation, $request, $data), ResourceControllerEvents::RESOURCE_CONTROLLER)
        ;
        $this->responseHandler
            ->expects(self::once())
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
            ->expects(self::once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->eventDispatcher
            ->expects(self::once())
            ->method('dispatchEvents')
            ->willReturnCallback(function (
                ResourceControllerEvent $event,
                string $expectedEventPattern,
            ) use ($operation): void {
                self::assertEquals(ResourceControllerEvents::RESOURCE_CONTROLLER, $expectedEventPattern);
                $event->setResponse(new Response(content: 'some event html'));
            })
        ;
        $this->responseHandler
            ->expects(self::never())
            ->method('createResponse')
        ;
        $response = $this->controller->__invoke($operation, $request);

        self::assertEquals('some event html', $response->getContent());
    }

    protected function setUp(): void
    {
        $this->provider = self::createMock(ProviderInterface::class);
        $this->eventDispatcher = self::createMock(ResourceEventDispatcherInterface::class);
        $this->responseHandler = self::createMock(ResponseHandlerInterface::class);
        $this->controller = new ShowResource(
            $this->provider,
            $this->eventDispatcher,
            $this->responseHandler,
        );
    }
}
