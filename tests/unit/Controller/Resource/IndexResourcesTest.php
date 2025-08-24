<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Resource;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Controller\Resource\IndexResources;
use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
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

    #[Test]
    public function itListResources(): void
    {
        $request = new Request();

        $resource = new Resource(name: 'my_resource', application: 'my_application');
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
        $gridView = new GridView(
            name: 'my_grid',
            type: 'some_type',
            headers: [],
            rows: [],
            template: '',
        );
        $data = new ArrayCollection([new \stdClass()]);

        $this->contextBuilder
            ->expects($this->once())
            ->method('buildContext')
            ->with($operation)
            ->willReturn(['a_context_key' => 'a_context_value'])
        ;
        $this->formFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap([
                [CollectionType::class, $data, ['entry_type' => 'MyForm', 'entry_options' => ['some_option' => 'some_value']], $form],
                ['MyFilterForm', null, ['some_other_option' => 'some_other_value'], $filterForm],
            ])
        ;
        $form->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(false)
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

        $this->provider
            ->expects($this->once())
            ->method('provide')
            ->with($operation, [], [
                'filters' => ['filter_key' => 'filter_value'],
                'a_context_key' => 'a_context_value',
            ])
            ->willReturn($data)
        ;
        $this->gridBuilder
            ->expects($this->once())
            ->method('build')
            ->with($operation, $data, [
                'filters' => ['filter_key' => 'filter_value'],
                'a_context_key' => 'a_context_value',
            ])
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
            ->with($operation, $data, [
                'form' => $form,
                'filterForm' => $filterForm,
                'grid' => $gridView,
            ])
            ->willReturn(new Response(content: '<p>content</p>'))
        ;

        $response = $this->controller->__invoke($request, $operation);

        self::assertEquals('<p>content</p>', $response->getContent());
    }

    #[Test]
    public function itListResourcesWithEvent(): void
    {
        $resource = new Resource(name: 'my_resource', application: 'my_application');
        $request = new Request();

        $operation = new Index(
            template: 'my_template.html.twig',
            form: 'MyForm',
            formOptions: ['some_option' => 'some_value'],
            grid: 'my_grid',
            filterForm: null,
        )->setResource($resource);

        $form = $this->createMock(FormInterface::class);
        $grid = new GridView(
            name: 'my_grid',
            type: 'some_type',
            headers: [],
            rows: [],
            template: '',
        );
        $data = new ArrayCollection([new \stdClass()]);

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
        $this->provider
            ->expects($this->once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->gridBuilder
            ->expects($this->once())
            ->method('build')
            ->with($operation, $data)
            ->willReturn($grid)
        ;

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchEvents')
            ->willReturnCallback(function (ResourceControllerEvent $event, string $eventName) use ($operation, $request, $data): void {
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

        $response = $this->controller->__invoke($request, $operation);

        self::assertEquals('<p>some event content</p>', $response->getContent());
    }

    #[Test]
    public function itProcessAForm(): void
    {
        $resource = new Resource(name: 'my_resource', application: 'my_application');
        $request = new Request();

        $operation = new Index(
            template: 'my_template.html.twig',
            form: 'MyForm',
            formOptions: ['some_option' => 'some_value'],
            filterForm: null,
        )->setResource($resource);

        $data = new ArrayCollection([new \stdClass()]);

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
            ->with($operation)
            ->willReturn($data)
        ;
        $this->processor
            ->expects($this->once())
            ->method('process')
            ->with($data, $operation)
        ;
        $this->responseHandler
            ->expects($this->once())
            ->method('createRedirectResponse')
            ->with($operation, $data)
            ->willReturn(new RedirectResponse(url: '/some-url'))
        ;

        $response = $this->controller->__invoke($request, $operation);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals('/some-url', $response->getTargetUrl());
    }

    protected function setUp(): void
    {
        $this->contextBuilder = $this->createMock(ContextBuilderInterface::class);
        $this->provider = $this->createMock(ProviderInterface::class);
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->gridBuilder = $this->createMock(GridViewBuilderInterface::class);
        $this->eventDispatcher = $this->createMock(ResourceEventDispatcherInterface::class);
        $this->responseHandler = $this->createMock(ResponseHandlerInterface::class);
        $this->controller = new IndexResources(
            $this->contextBuilder,
            $this->provider,
            $this->processor,
            $this->formFactory,
            $this->gridBuilder,
            $this->eventDispatcher,
            $this->responseHandler,
        );
    }
}
