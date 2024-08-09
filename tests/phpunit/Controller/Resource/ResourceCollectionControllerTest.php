<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Resource;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Controller\Resource\ResourceCollectionController;
use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Response\Handler\RedirectHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

final class ResourceCollectionControllerTest extends TestCase
{
    private ResourceCollectionController $controller;
    private MockObject $uriVariablesExtractor;
    private MockObject $contextProvider;
    private MockObject $provider;
    private MockObject $processor;
    private MockObject $redirectionHandler;
    private MockObject $gridRegistry;
    private MockObject $gridViewBuilder;
    private MockObject $formFactory;
    private MockObject $environment;

    public function testInvoke(): void
    {
        $resource = new Resource();
        $request = new Request();
        $operation = (new Index(
            template: 'my_template.html.twig',
            form: 'MyForm',
            formOptions: ['some_option' => 'some_value'],
            filterForm: 'MyFilterForm',
            filterFormOptions: ['some_other_option' => 'some_other_value'],
            grid: 'my_grid',
        ))->withResource($resource);

        $form = self::createMock(FormInterface::class);
        $formView = self::createMock(FormView::class);

        $filterForm = self::createMock(FormInterface::class);
        $filterFormView = self::createMock(FormView::class);

        $grid = new Grid();
        $gridView = new GridView(
            name: 'my_grid',
            type: 'some_type',
            template: '',
            headers: [],
            rows: [],
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
        $form->expects(self::once())
            ->method('createView')
            ->willReturn($formView)
        ;
        $filterForm->expects(self::once())
            ->method('handleRequest')
            ->with($request)
        ;
        $filterForm->expects(self::once())
            ->method('createView')
            ->willReturn($filterFormView)
        ;

        $this->uriVariablesExtractor
            ->expects(self::once())
            ->method('extractVariables')
            ->with($operation, $request)
            ->willReturn(['id' => 'test_id'])
        ;
        $this->contextProvider
            ->expects(self::once())
            ->method('getContext')
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
            ->with($grid)
            ->willReturn($gridView)
        ;


        $this
            ->environment
            ->expects(self::once())
            ->method('render')
            ->with('my_template.html.twig', [
                'grid' => $gridView,
                'resource' => $resource,
                'operation' => $operation,
                'data' => $data,
                'form' => $formView,
                'filterForm' => $filterFormView,
            ])
            ->willReturn('<p>content</p>')
        ;

        $response = $this->controller->__invoke($request, $operation);

        self::assertEquals('<p>content</p>', $response->getContent());
    }

    protected function setUp(): void
    {
        $this->uriVariablesExtractor = self::createMock(UriVariablesExtractorInterface::class);
        $this->contextProvider = self::createMock(ContextProviderInterface::class);
        $this->provider = self::createMock(ProviderInterface::class);
        $this->processor = self::createMock(ProcessorInterface::class);
        $this->redirectionHandler = self::createMock(RedirectHandlerInterface::class);
        $this->gridRegistry = self::createMock(GridRegistryInterface::class);
        $this->gridViewBuilder = self::createMock(GridViewBuilderInterface::class);
        $this->formFactory = self::createMock(FormFactoryInterface::class);
        $this->serializer = self::createMock(SerializerInterface::class);
        $this->environment = self::createMock(Environment::class);
        $this->controller = new ResourceCollectionController(
            $this->uriVariablesExtractor,
            $this->contextProvider,
            $this->provider,
            $this->processor,
            $this->redirectionHandler,
            $this->gridRegistry,
            $this->gridViewBuilder,
            $this->formFactory,
            $this->serializer,
            $this->environment,
        );
    }
}
