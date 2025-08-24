<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Resource;

use LAG\AdminBundle\Controller\Resource\ProcessResource;
use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\Entity\FakeEntity;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ProcessResourceTest extends TestCase
{
    private ProcessResource $controller;
    private MockObject $contextBuilder;
    private MockObject $provider;
    private MockObject $processor;
    private MockObject $formFactory;
    private MockObject $eventDispatcher;
    private MockObject $responseHandler;

    #[Test]
    #[DataProvider('operations')]
    public function itShowResourceForm(OperationInterface $operation): void
    {
        $request = new Request();
        $data = new FakeEntity();
        $data->id = 666;
        $event = new ResourceControllerEvent($operation, $request, $data);
        $form = $this->createMock(FormInterface::class);

        $this->contextBuilder
            ->expects($this->once())
            ->method('buildContext')
            ->with($operation, $request)
        ;
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with($operation->getForm(), $data, $operation->getFormOptions())
            ->willReturn($form)
        ;
        $this->provider
            ->expects($this->once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->processor
            ->expects($this->never())
            ->method('process')
        ;
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchEvents')
            ->with($event, ResourceControllerEvents::RESOURCE_CONTROLLER)
        ;
        $this->responseHandler
            ->expects($this->once())
            ->method('createResponse')
            ->with($operation, $data, ['form' => $form])
            ->willReturn(new Response(content: 'some html'))
        ;
        $response = $this->controller->__invoke($operation, $request);

        self::assertEquals('some html', $response->getContent());
    }

    #[Test]
    #[DataProvider('operations')]
    public function itReturnsAResponseFromEvent(OperationInterface $operation): void
    {
        $request = new Request();
        $data = new FakeEntity();
        $data->id = 666;
        $form = $this->createMock(FormInterface::class);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with($operation->getForm(), $data, $operation->getFormOptions())
            ->willReturn($form)
        ;
        $this->provider
            ->expects($this->once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->processor
            ->expects($this->never())
            ->method('process')
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

    #[Test]
    #[DataProvider('operations')]
    public function itHandlesASubmittedForm(OperationInterface $operation): void
    {
        $request = new Request();
        $data = new FakeEntity();
        $data->id = 666;

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $form->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;
        $form->expects($this->once())
            ->method('getData')
            ->willReturn($data)
        ;

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with($operation->getForm(), $data, $operation->getFormOptions())
            ->willReturn($form)
        ;
        $this->provider
            ->expects($this->once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->processor
            ->expects($this->once())
            ->method('process')
            ->with($data, $operation)
        ;
        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatchEvents')
        ;
        $this->responseHandler
            ->expects($this->once())
            ->method('createRedirectResponse')
            ->with($operation, $data, ['form' => $form])
            ->willReturn(new RedirectResponse(url: '/url'))
        ;
        $response = $this->controller->__invoke($operation, $request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/url', $response->getTargetUrl());
    }

    public static function operations(): iterable
    {
        $create = new Create(
            name: 'my_create_operation',
            template: 'create.html.twig',
            normalizationContext: ['groups' => ['my_group']],
            form: 'CreateForm',
            formOptions: ['an_option' => 'a_value'],
        );
        $update = new Create(
            name: 'my_update_operation',
            template: 'update.html.twig',
            normalizationContext: ['groups' => ['my_group']],
            form: 'UpdateForm',
            formOptions: ['an_option' => 'a_value'],
        );
        $delete = new Create(
            name: 'my_delete_operation',
            template: 'delete.html.twig',
            normalizationContext: ['groups' => ['my_group']],
            form: 'DeleteForm',
            formOptions: ['an_option' => 'a_value'],
        );

        $resource = new Resource(
            name: 'my_resource',
            operations: [$create, $update, $delete],
            application: 'my_application',
        );

        foreach ($resource->getOperations() as $operation) {
            yield [$operation];
        }
    }

    protected function setUp(): void
    {
        $this->contextBuilder = $this->createMock(ContextBuilderInterface::class);
        $this->provider = $this->createMock(ProviderInterface::class);
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->eventDispatcher = $this->createMock(ResourceEventDispatcherInterface::class);
        $this->responseHandler = $this->createMock(ResponseHandlerInterface::class);
        $this->controller = new ProcessResource(
            $this->contextBuilder,
            $this->provider,
            $this->processor,
            $this->formFactory,
            $this->eventDispatcher,
            $this->responseHandler,
        );
    }
}
