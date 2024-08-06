<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Resource;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Controller\Resource\ResourceCollectionController;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

// TODO update test when grid is merged
final class ResourceCollectionControllerTest extends TestCase
{
    private ResourceCollectionController $controller;
    private MockObject $uriVariablesExtractor;
    private MockObject $contextProvider;
    private MockObject $provider;
    private MockObject $processor;
    private MockObject $redirectionHandler;
    private MockObject $gridRegistry;
    private MockObject $formFactory;
    private MockObject $environment;

    public function testInvoke(): void
    {
        $resource = new Resource();
        $request = new Request();
        $operation = (new Index())
            ->withTemplate('my_template.html.twig')
            ->withFilterFormType('FormClass')
            ->withFilterFormOptions(['label' => 'my_form'])
            ->withResource($resource)
        ;

        $form = $this->createMock(FormInterface::class);
        $formView = $this->createMock(FormView::class);
        $grid = new GridView(
            name: '',
            template: '',
            headers: [],
            rows: [],
        );

        $data = new ArrayCollection([new \stdClass()]);

        $this
            ->uriVariablesExtractor
            ->expects($this->once())
            ->method('extractVariables')
            ->with($operation, $request)
            ->willReturn(['id' => 'test_id'])
        ;

        $this
            ->contextProvider
            ->expects($this->once())
            ->method('getContext')
            ->with($operation, $request)
            ->willReturn(['a_context' => 'a_value'])
        ;

        $this
            ->formFactory
            ->expects($this->once())
            ->method('create')
            ->with('FormClass', [], ['label' => 'my_form'])
            ->willReturn($form)
        ;

        $form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($request)
        ;

        $this
            ->dataProvider
            ->expects($this->once())
            ->method('provide')
            ->with($operation, ['id' => 'test_id'], ['a_context' => 'a_value'])
            ->willReturn($data)
        ;

        $this
            ->gridFactory
            ->expects($this->once())
            ->method('leagacybuildView')
            ->with($operation, $data)
            ->willReturn($grid)
        ;

        $form
            ->expects($this->once())
            ->method('createView')
            ->willReturn($formView)
        ;

        $this
            ->environment
            ->expects($this->once())
            ->method('render')
            ->with('my_template.html.twig', [
                'grid' => $grid,
                'resource' => $resource,
                'operation' => $operation,
                'data' => $data,
                'form' => $formView,
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
        $this->dataProvider = self::createMock(ProviderInterface::class);
        $this->gridFactory = self::createMock(GridViewBuilderInterface::class);
        $this->formFactory = self::createMock(FormFactoryInterface::class);
        $this->serializer = self::createMock(SerializerInterface::class);
        $this->environment = self::createMock(Environment::class);
        $this->controller = new ResourceCollectionController(
            $this->uriVariablesExtractor,
            $this->contextProvider,
            $this->dataProvider,
            $this->gridFactory,
            $this->formFactory,
            $this->serializer,
            $this->environment,
        );
    }
}
