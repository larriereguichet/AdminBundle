<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Resource;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Controller\Resource\ResourceCollectionController;
use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use LAG\AdminBundle\Grid\GridView;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\GetCollection;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\State\Provider\DataProviderInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

class ResourceCollectionControllerTest extends TestCase
{
    private ResourceCollectionController $controller;
    private MockObject $uriVariablesExtractor;
    private MockObject $contextProvider;
    private MockObject $dataProvider;
    private MockObject $formFactory;
    private MockObject $gridFactory;
    private MockObject $environment;

    public function testInvoke(): void
    {
        $resource = new AdminResource();
        $request = new Request();
        $operation = (new GetCollection())
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
            ->method('create')
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

        $this->assertEquals('<p>content</p>', $response->getContent());
    }

    protected function setUp(): void
    {
        $this->uriVariablesExtractor = $this->createMock(UriVariablesExtractorInterface::class);
        $this->contextProvider = $this->createMock(ContextProviderInterface::class);
        $this->dataProvider = $this->createMock(DataProviderInterface::class);
        $this->gridFactory = $this->createMock(GridFactoryInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->environment = $this->createMock(Environment::class);
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
