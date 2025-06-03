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
    private MockObject $provider;
    private MockObject $processor;
    private MockObject $formFactory;
    private MockObject $eventDispatcher;
    private MockObject $responseHandler;

    #[Test]
    #[DataProvider(methodName: 'operations')]
    public function itShowResourceForm(OperationInterface $operation): void
    {
        $request = new Request();
        $data = new FakeEntity();
        $data->id = 666;
        $event = new ResourceControllerEvent($operation, $request, $data);
        $form = self::createMock(FormInterface::class);

        $this->formFactory
            ->expects(self::once())
            ->method('create')
            ->with($operation->getForm(), $data, $operation->getFormOptions())
            ->willReturn($form)
        ;
        $this->provider
            ->expects(self::once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->processor
            ->expects(self::never())
            ->method('process')
        ;
        $this->eventDispatcher
            ->expects(self::once())
            ->method('dispatchEvents')
            ->with($event, ResourceControllerEvents::RESOURCE_CONTROLLER)
        ;
        $this->responseHandler
            ->expects(self::once())
            ->method('createResponse')
            ->with($operation, $data, ['form' => $form])
            ->willReturn(new Response(content: 'some html'))
        ;
        $response = $this->controller->__invoke($operation, $request);

        self::assertEquals('some html', $response->getContent());
    }

    #[Test]
    #[DataProvider(methodName: 'operations')]
    public function itReturnsAResponseFromEvent(OperationInterface $operation): void
    {
        $request = new Request();
        $data = new FakeEntity();
        $data->id = 666;
        $form = self::createMock(FormInterface::class);

        $this->formFactory
            ->expects(self::once())
            ->method('create')
            ->with($operation->getForm(), $data, $operation->getFormOptions())
            ->willReturn($form)
        ;
        $this->provider
            ->expects(self::once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->processor
            ->expects(self::never())
            ->method('process')
        ;
        $this->eventDispatcher
            ->expects(self::once())
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
            ->expects(self::never())
            ->method('createResponse')
        ;
        $response = $this->controller->__invoke($operation, $request);

        self::assertEquals('some event html', $response->getContent());
    }

    #[Test]
    #[DataProvider(methodName: 'operations')]
    public function itHandlesASubmittedForm(OperationInterface $operation): void
    {
        $request = new Request();
        $data = new FakeEntity();
        $data->id = 666;

        $form = self::createMock(FormInterface::class);
        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $form->expects(self::once())
            ->method('isValid')
            ->willReturn(true)
        ;
        $form->expects(self::once())
            ->method('getData')
            ->willReturn($data)
        ;

        $this->formFactory
            ->expects(self::once())
            ->method('create')
            ->with($operation->getForm(), $data, $operation->getFormOptions())
            ->willReturn($form)
        ;
        $this->provider
            ->expects(self::once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->processor
            ->expects(self::once())
            ->method('process')
            ->with($data, $operation)
        ;
        $this->eventDispatcher
            ->expects(self::never())
            ->method('dispatchEvents')
        ;
        $this->responseHandler
            ->expects(self::once())
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
            shortName: 'my_create_operation',
            template: 'create.html.twig',
            normalizationContext: ['groups' => ['my_group']],
            form: 'CreateForm',
            formOptions: ['an_option' => 'a_value'],
        );
        $update = new Create(
            shortName: 'my_update_operation',
            template: 'update.html.twig',
            normalizationContext: ['groups' => ['my_group']],
            form: 'UpdateForm',
            formOptions: ['an_option' => 'a_value'],
        );
        $delete = new Create(
            shortName: 'my_delete_operation',
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
        $this->provider = self::createMock(ProviderInterface::class);
        $this->processor = self::createMock(ProcessorInterface::class);
        $this->formFactory = self::createMock(FormFactoryInterface::class);
        $this->eventDispatcher = self::createMock(ResourceEventDispatcherInterface::class);
        $this->responseHandler = self::createMock(ResponseHandlerInterface::class);
        $this->controller = new ProcessResource(
            $this->provider,
            $this->processor,
            $this->formFactory,
            $this->eventDispatcher,
            $this->responseHandler,
        );
    }
}
