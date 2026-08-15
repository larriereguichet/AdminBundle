<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Controller\Resource;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Controller\Resource\IndexResources;
use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\ViewBuilder\GridBuilderInterface;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\ResourceInterface;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexResourcesTest extends TestCase
{
    private IndexResources $controller;
    private MockObject $contextBuilder;
    private MockObject $provider;
    private MockObject $processor;
    private MockObject $formFactory;
    private MockObject $gridBuilder;
    private MockObject $eventDispatcher;
    private MockObject $responseHandler;
    private MockObject $security;

    #[Test]
    public function itListResources(): void
    {
        $request = new Request();

        $resource = new Resource(shortName: 'my_resource', application: 'my_application');
        $operation = new Index(
            template: 'my_template.html.twig',
            form: 'MyForm',
            formOptions: ['some_option' => 'some_value'],
            grid: 'my_grid',
            filterForm: 'MyFilterForm',
            filterFormOptions: ['some_other_option' => 'some_other_value'],
        )->setResource($resource);

        $form = $this->createMock(FormInterface::class);
        $filterForm = $this->createMock(FormInterface::class);
        $formView = new FormView();
        $filterFormView = new FormView();
        $gridMeta = $this->createStub(GridInterface::class);
        $gridView = $this->createStub(GridView::class);
        $data = new ArrayCollection([new \stdClass()]);
        $context = ['filters' => ['filter_key' => 'filter_value'], 'a_context_key' => 'a_context_value'];

        $this->processor
            ->expects($this->never())
            ->method('process')
        ;
        $this->contextBuilder
            ->expects($this->once())
            ->method('buildContext')
            ->with($request, $operation, $gridMeta)
            ->willReturn(['a_context_key' => 'a_context_value'])
        ;
        $this->formFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap([
                ['MyFilterForm', null, ['some_other_option' => 'some_other_value'], $filterForm],
                [CollectionType::class, $data, ['entry_type' => 'MyForm', 'entry_options' => ['some_option' => 'some_value']], $form],
            ])
        ;
        $filterForm->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $filterForm->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $filterForm->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;
        $filterForm->expects($this->once())
            ->method('getData')
            ->willReturn(['filter_key' => 'filter_value'])
        ;
        $filterForm->expects($this->once())
            ->method('createView')
            ->willReturn($filterFormView)
        ;
        $this->provider
            ->expects($this->once())
            ->method('provide')
            ->with($operation, [], $context)
            ->willReturn($data)
        ;
        $form->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(false)
        ;
        $form->expects($this->once())
            ->method('createView')
            ->willReturn($formView)
        ;
        $this->gridBuilder
            ->expects($this->once())
            ->method('build')
            ->with($gridMeta, $operation, $data, $context)
            ->willReturn($gridView)
        ;
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchEvents')
            ->with(new ResourceControllerEvent($operation, $request, $data), ResourceControllerEvents::RESOURCE_CONTROLLER)
        ;
        $this->responseHandler
            ->expects($this->once())
            ->method('createResponse')
            ->with($request, $operation, $data, [
                'form' => $formView,
                'filterForm' => $filterFormView,
                'batchForm' => null,
                'grid' => $gridView,
            ])
            ->willReturn(new Response(content: '<p>content</p>'))
        ;

        $response = $this->controller->__invoke($request, $operation, $gridMeta);

        self::assertEquals('<p>content</p>', $response->getContent());
    }

    #[Test]
    public function itListResourcesWithEvent(): void
    {
        $this->processor
            ->expects($this->never())
            ->method('process')
        ;
        $resource = new Resource(shortName: 'my_resource', application: 'my_application');
        $request = new Request();

        $operation = new Index(
            template: 'my_template.html.twig',
            form: 'MyForm',
            formOptions: ['some_option' => 'some_value'],
            grid: 'my_grid',
            filterForm: null,
        )->setResource($resource);

        $form = $this->createMock(FormInterface::class);
        $gridMeta = $this->createStub(GridInterface::class);
        $grid = $this->createStub(GridView::class);
        $data = new ArrayCollection([new \stdClass()]);
        $context = [];

        $this->contextBuilder
            ->expects($this->once())
            ->method('buildContext')
            ->with($request, $operation, $gridMeta)
            ->willReturn($context)
        ;
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(CollectionType::class, $data, [
                'entry_type' => 'MyForm',
                'entry_options' => ['some_option' => 'some_value'],
            ])
            ->willReturn($form)
        ;
        $form->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(false)
        ;
        $this->provider
            ->expects($this->once())
            ->method('provide')
            ->with($operation, [], $context)
            ->willReturn($data)
        ;
        $this->gridBuilder
            ->expects($this->once())
            ->method('build')
            ->with($gridMeta, $operation, $data, $context)
            ->willReturn($grid)
        ;
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchEvents')
            ->willReturnCallback(static function (ResourceControllerEvent $event, string $eventName) use ($operation, $request, $data): void {
                self::assertEquals($operation, $event->getOperation());
                self::assertEquals($request, $event->getRequest());
                self::assertEquals($data, $event->getData());
                self::assertEquals(ResourceControllerEvents::RESOURCE_CONTROLLER, $eventName);
                $event->setResponse(new Response('<p>some event content</p>'));
            })
        ;
        $this->responseHandler
            ->expects($this->never())
            ->method('createResponse')
        ;

        $response = $this->controller->__invoke($request, $operation, $gridMeta);

        self::assertEquals('<p>some event content</p>', $response->getContent());
    }

    #[Test]
    public function itProcessAForm(): void
    {
        $this->gridBuilder->expects($this->never())->method('build');
        $this->eventDispatcher->expects($this->never())->method('dispatchEvents');
        $resource = new Resource(shortName: 'my_resource', application: 'my_application');
        $request = new Request();

        $operation = new Index(
            template: 'my_template.html.twig',
            form: 'MyForm',
            formOptions: ['some_option' => 'some_value'],
            filterForm: null,
        )->setResource($resource);

        $data = new ArrayCollection([new \stdClass()]);
        $context = [];

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())
            ->method('getData')
            ->willReturn($data)
        ;
        $form->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $form->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;

        $this->contextBuilder
            ->expects($this->once())
            ->method('buildContext')
            ->with($request, $operation, null)
            ->willReturn($context)
        ;
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(CollectionType::class, $data, [
                'entry_type' => 'MyForm',
                'entry_options' => ['some_option' => 'some_value'],
            ])
            ->willReturn($form)
        ;
        $this->provider
            ->expects($this->once())
            ->method('provide')
            ->with($operation, [], $context)
            ->willReturn($data)
        ;
        $this->processor
            ->expects($this->once())
            ->method('process')
            ->with($data, $operation, [], $context)
        ;
        $this->responseHandler
            ->expects($this->once())
            ->method('createRedirectResponse')
            ->with($request, $operation, $data)
            ->willReturn(new RedirectResponse(url: '/some-url'))
        ;

        $response = $this->controller->__invoke($request, $operation);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals('/some-url', $response->getTargetUrl());
    }

    #[Test]
    public function itProcessesBatchOperations(): void
    {
        $request = new Request([], ['batch_ids' => ['1', '2']]);
        $context = [];
        $entity = new \stdClass();

        $targetOperation = $this->createStub(OperationInterface::class);
        $resource = $this->createStub(ResourceInterface::class);
        $resource->method('getIdentifiers')->willReturn(['id']);
        $resource->method('getOperation')->willReturn($targetOperation);

        $operation = $this->createStub(CollectionOperationInterface::class);
        $operation->method('getBatchOperations')->willReturn(['delete']);
        $operation->method('getFilterForm')->willReturn(null);
        $operation->method('getForm')->willReturn(null);
        $operation->method('getResource')->willReturn($resource);

        $batchForm = $this->createMock(FormInterface::class);
        $batchOperationField = $this->createMock(FormInterface::class);
        $batchOperationField->method('getData')->willReturn('delete');
        $batchForm->method('get')->with('operation')->willReturn($batchOperationField);
        $batchForm->method('isSubmitted')->willReturn(true);
        $batchForm->method('isValid')->willReturn(true);
        $batchForm->method('handleRequest')->willReturnSelf();

        $this->contextBuilder->method('buildContext')->willReturn($context);
        $this->formFactory->method('create')->willReturn($batchForm);
        $this->provider->method('provide')->willReturn($entity);
        $this->processor->expects($this->exactly(2))->method('process');
        $this->responseHandler
            ->expects($this->once())
            ->method('createRedirectResponse')
            ->with($request, $operation, null)
            ->willReturn(new RedirectResponse('/list'))
        ;

        $response = $this->controller->__invoke($request, $operation);

        self::assertInstanceOf(RedirectResponse::class, $response);
    }

    protected function setUp(): void
    {
        $this->contextBuilder = $this->createMock(ContextBuilderInterface::class);
        $this->provider = $this->createMock(ProviderInterface::class);
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->gridBuilder = $this->createMock(GridBuilderInterface::class);
        $this->eventDispatcher = $this->createMock(ResourceEventDispatcherInterface::class);
        $this->responseHandler = $this->createMock(ResponseHandlerInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->security->method('isGranted')->willReturn(true);
        $this->controller = new IndexResources(
            $this->contextBuilder,
            $this->provider,
            $this->processor,
            $this->formFactory,
            $this->gridBuilder,
            $this->eventDispatcher,
            $this->responseHandler,
            $this->security,
        );
    }
}
