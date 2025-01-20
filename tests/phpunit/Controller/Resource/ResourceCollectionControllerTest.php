<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Resource;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Controller\Resource\ResourceCollectionController;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Request\Uri\UrlVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResourceCollectionControllerTest extends TestCase
{
    private ResourceCollectionController $controller;
    private MockObject $uriVariablesExtractor;
    private MockObject $contextProvider;
    private MockObject $provider;
    private MockObject $processor;
    private MockObject $responseHandler;
    private MockObject $gridRegistry;
    private MockObject $gridViewBuilder;
    private MockObject $formFactory;
    private MockObject $eventDispatcher;

    public function testInvoke(): void
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

        $form = self::createMock(FormInterface::class);
        $filterForm = self::createMock(FormInterface::class);

        $grid = new Grid();
        $gridView = new GridView(
            name: 'my_grid',
            type: 'some_type',
            headers: [],
            rows: [],
            template: '',
        );

        $data = new ArrayCollection([new \stdClass()]);

        $map = [
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
        ];

        $this->formFactory
            ->expects(self::exactly(2))
            ->method('create')
            ->willReturnMap($map)
        ;
        $form->expects(self::once())
            ->method('handleRequest')
            ->with($request)
        ;
        $filterForm->expects(self::once())
            ->method('handleRequest')
            ->with($request)
        ;

        $this->uriVariablesExtractor
            ->expects(self::once())
            ->method('extractVariables')
            ->with($operation, $request)
            ->willReturn(['id' => 'test_id'])
        ;
        $this->contextProvider
            ->expects(self::once())
            ->method('buildContext')
            ->with($operation, $request)
            ->willReturn(['a_context' => 'a_value'])
        ;
        $this->provider
            ->expects(self::once())
            ->method('provide')
            ->with($operation, ['id' => 'test_id'], ['a_context' => 'a_value'])
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
        $this->responseHandler
            ->expects(self::once())
            ->method('createCollectionResponse')
            ->with(
                $request,
                $operation,
                $data,
                $form,
                $filterForm,
                $gridView,
            )
            ->willReturn(new Response(content: '<p>content</p>'))
        ;

        $response = $this->controller->__invoke($request, $operation); // @phpstan-ignore-line

        self::assertEquals('<p>content</p>', $response->getContent());
    }

    protected function setUp(): void
    {
        $this->uriVariablesExtractor = self::createMock(UrlVariablesExtractorInterface::class);
        $this->contextProvider = self::createMock(ContextBuilderInterface::class);
        $this->provider = self::createMock(ProviderInterface::class);
        $this->processor = self::createMock(ProcessorInterface::class);
        $this->responseHandler = self::createMock(ResponseHandlerInterface::class);
        $this->gridRegistry = self::createMock(GridRegistryInterface::class);
        $this->gridViewBuilder = self::createMock(GridViewBuilderInterface::class);
        $this->formFactory = self::createMock(FormFactoryInterface::class);
        $this->eventDispatcher = self::createMock(ResourceEventDispatcherInterface::class);
        $this->controller = new ResourceCollectionController(
            $this->uriVariablesExtractor,
            $this->contextProvider,
            $this->provider,
            $this->processor,
            $this->gridRegistry,
            $this->gridViewBuilder,
            $this->formFactory,
            $this->eventDispatcher,
            $this->responseHandler,
        );
    }
}
