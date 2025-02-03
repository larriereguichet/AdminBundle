<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Resource;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Controller\Resource\IndexResources;
use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
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
    private MockObject $provider;
    private MockObject $processor;
    private MockObject $responseHandler;
    private MockObject $gridRegistry;
    private MockObject $gridViewBuilder;
    private MockObject $formFactory;
    private MockObject $eventDispatcher;

    #[Test]
    public function itReturnsAResponse(): void
    {
        $resource = new Resource(name: 'my_resource', application: 'my_application');
        $request = new Request();

        $operation = (new Index(
            template: 'my_template.html.twig',
            form: 'MyForm',
            formOptions: ['some_option' => 'some_value'],
            grid: 'my_grid',
            filterForm: 'MyFilterForm',
            filterFormOptions: ['some_other_option' => 'some_other_value'],
        ))->withResource($resource);

        $formView = self::createMock(FormView::class);
        $form = self::createMock(FormInterface::class);
        $form->expects(self::once())
            ->method('createView')
            ->willReturn($formView)
        ;

        $filterFormView = self::createMock(FormView::class);
        $filterForm = self::createMock(FormInterface::class);
        $filterForm->expects(self::once())
            ->method('createView')
            ->willReturn($filterFormView)
        ;

        $grid = new Grid();
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
                [
                    CollectionType::class,
                    $data,
                    ['entry_type' => 'MyForm', 'entry_options' => ['some_option' => 'some_value']],
                    $form,
                ],
                [
                    'MyFilterForm',
                    [],
                    ['some_other_option' => 'some_other_value'],
                    $filterForm,
                ],
            ])
        ;
        $form->expects(self::once())
            ->method('handleRequest')
            ->with($request)
        ;
        $filterForm->expects(self::once())
            ->method('handleRequest')
            ->with($request)
        ;

        $this->provider
            ->expects(self::once())
            ->method('provide')
            ->with($operation)
            ->willReturn($data)
        ;
        $this->gridRegistry
            ->expects(self::once())
            ->method('get')
            ->with('my_grid')
            ->willReturn($grid)
        ;
        $this->gridViewBuilder
            ->expects(self::once())
            ->method('build')
            ->with($operation, $grid)
            ->willReturn($gridView)
        ;
        $this->eventDispatcher
            ->expects(self::once())
            ->method('dispatchEvents')
            ->with(
                new ResourceControllerEvent($operation, $request, $data),
                ResourceControllerEvents::RESOURCE_CONTROLLER_PATTERN,
                $operation->getResource()->getApplication(),
                $operation->getResource()->getName(),
                $operation->getName(),
            )
        ;
        $this->responseHandler
            ->expects(self::once())
            ->method('createResponse')
            ->with($operation, $data, $request, [
                'form' => $formView,
                'filterForm' => $formView,
                'grid' => $gridView,
            ])
            ->willReturn(new Response(content: '<p>content</p>'))
        ;

        $response = $this->controller->__invoke($request, $operation);

        self::assertEquals('<p>content</p>', $response->getContent());
    }

    #[Test]
    public function itProcessAForm(): void
    {
        $resource = new Resource(name: 'my_resource', application: 'my_application');
        $request = new Request();

        $operation = (new Index(
            template: 'my_template.html.twig',
            form: 'MyForm',
            formOptions: ['some_option' => 'some_value'],
            filterForm: null,
        ))->withResource($resource);

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
            ->method('createResponse')
            ->with($operation, $data, $request)
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
        $this->responseHandler = self::createMock(ResponseHandlerInterface::class);
        $this->gridRegistry = self::createMock(GridRegistryInterface::class);
        $this->gridViewBuilder = self::createMock(GridViewBuilderInterface::class);
        $this->formFactory = self::createMock(FormFactoryInterface::class);
        $this->eventDispatcher = self::createMock(ResourceEventDispatcherInterface::class);
        $this->controller = new IndexResources(
            $this->provider,
            $this->processor,
            $this->formFactory,
            $this->gridRegistry,
            $this->gridViewBuilder,
            $this->eventDispatcher,
            $this->responseHandler,
        );
    }
}
