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
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Resource;
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
        )->withResource($resource);

        $form = self::createMock(FormInterface::class);
        $filterForm = self::createMock(FormInterface::class);
        $gridView = new GridView(
            name: 'my_grid',
            type: 'some_type',
            headers: [],
            rows: [],
            template: '',
        );
        $data = new ArrayCollection([new \stdClass()]);

        $this->formFactory
            ->expects(self::exactly(2))
            ->method('create')
            ->willReturnMap([
                [CollectionType::class, $data, ['entry_type' => 'MyForm', 'entry_options' => ['some_option' => 'some_value']], $form],
                ['MyFilterForm', null, ['some_other_option' => 'some_other_value'], $filterForm]
            ])
        ;
        $form->expects(self::once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(false)
        ;
        $filterForm->expects(self::once())
            ->method('handleRequest')
            ->with($request)
        ;
        $filterForm->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $filterForm->expects(self::once())
            ->method('isValid')
            ->willReturn(true)
        ;
        $filterForm->expects(self::once())
            ->method('getData')
            ->willReturn(['filter_key' => 'filter_value'])
        ;

        $this->provider
            ->expects(self::once())
            ->method('provide')
            ->with($operation, [], ['filters' => ['filter_key' => 'filter_value']])
            ->willReturn($data)
        ;
        $this->gridBuilder
            ->expects(self::once())
            ->method('build')
            ->with('my_grid', $operation)
            ->willReturn($gridView)
        ;
        $this->eventDispatcher
            ->expects(self::once())
            ->method('dispatchEvents')
            ->with(new ResourceControllerEvent($operation, $request, $data), ResourceControllerEvents::RESOURCE_CONTROLLER)
        ;
        $this->responseHandler
            ->expects(self::once())
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

        /** @var CollectionOperationInterface $operation */
        $operation = new Index(
            template: 'my_template.html.twig',
            form: 'MyForm',
            formOptions: ['some_option' => 'some_value'],
            grid: 'my_grid',
            filterForm: null,
        )->withResource($resource);

        $form = self::createMock(FormInterface::class);
        $grid = new GridView(
            name: 'my_grid',
            type: 'some_type',
            headers: [],
            rows: [],
            template: '',
        );
        $data = new ArrayCollection([new \stdClass()]);

        $this->formFactory
            ->expects(self::once())
            ->method('create')
            ->with(CollectionType::class, $data, [
                'entry_type' => 'MyForm',
                'entry_options' => ['some_option' => 'some_value'],
            ])
            ->willReturn($form)
        ;
        $form->expects(self::once())
            ->method('handleRequest')
            ->with($request)
        ;
        $this->provider
            ->expects(self::once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->gridBuilder
            ->expects(self::once())
            ->method('build')
            ->with('my_grid', $operation)
            ->willReturn($grid)
        ;

        $this->eventDispatcher
            ->expects(self::once())
            ->method('dispatchEvents')
            ->willReturnCallback(function (ResourceControllerEvent $event, string $eventName) use ($operation, $request, $data) {
                self::assertEquals($operation, $event->getOperation());
                self::assertEquals($request, $event->getRequest());
                self::assertEquals($data, $event->getData());
                self::assertEquals(ResourceControllerEvents::RESOURCE_CONTROLLER, $eventName);
                $event->setResponse(new Response('<p>some event content</p>'));
            })
        ;
        $this->responseHandler
            ->expects(self::never())
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

        /** @var CollectionOperationInterface $operation */
        $operation = new Index(
            template: 'my_template.html.twig',
            form: 'MyForm',
            formOptions: ['some_option' => 'some_value'],
            filterForm: null,
        )->withResource($resource);

        $data = new ArrayCollection([new \stdClass()]);

        $form = self::createMock(FormInterface::class);
        $form->expects(self::once())
            ->method('getData')
            ->willReturn($data)
        ;
        $form->expects(self::once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $form->expects(self::once())
            ->method('isValid')
            ->willReturn(true)
        ;

        $this->formFactory
            ->expects(self::once())
            ->method('create')
            ->with(CollectionType::class, $data, [
                'entry_type' => 'MyForm',
                'entry_options' => ['some_option' => 'some_value'],
            ])
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
        $this->responseHandler
            ->expects(self::once())
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
        $this->provider = self::createMock(ProviderInterface::class);
        $this->processor = self::createMock(ProcessorInterface::class);
        $this->formFactory = self::createMock(FormFactoryInterface::class);
        $this->gridBuilder = self::createMock(GridViewBuilderInterface::class);
        $this->eventDispatcher = self::createMock(ResourceEventDispatcherInterface::class);
        $this->responseHandler = self::createMock(ResponseHandlerInterface::class);
        $this->controller = new IndexResources(
            $this->provider,
            $this->processor,
            $this->formFactory,
            $this->gridBuilder,
            $this->eventDispatcher,
            $this->responseHandler,
        );
    }
}
