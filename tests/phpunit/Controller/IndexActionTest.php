<?php

namespace LAG\AdminBundle\Tests\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Controller\Index;
use LAG\AdminBundle\Exception\Operation\InvalidCollectionOperationException;
use LAG\AdminBundle\Form\Type\ResourceFilterType;
use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use LAG\AdminBundle\Grid\Grid;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\State\DataProviderInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class IndexActionTest extends TestCase
{
    private Index $controller;
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
        $operation = (new \LAG\AdminBundle\Metadata\Index())
            ->withTemplate('my_template.html.twig')
            ->withFormType('FormClass')
            ->withFormOptions(['label' => 'my_form'])
        ;

        $resource = $resource->withCurrentOperation($operation);
        $form = $this->createMock(FormInterface::class);
        $formView = $this->createMock(FormView::class);
        $grid = $this->createMock(Grid::class);

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
        $form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(false)
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

        $response = $this->controller->__invoke($request, $resource);

        $this->assertEquals('<p>content</p>', $response->getContent());
    }

    public function testInvokeWithWrongOperationType(): void
    {
        $admin = new AdminResource();
        $admin = $admin->withCurrentOperation(new Create());

        $this->expectException(InvalidCollectionOperationException::class);
        $this->controller->__invoke(new Request(), $admin);
    }

    protected function setUp(): void
    {
        $this->uriVariablesExtractor = $this->createMock(UriVariablesExtractorInterface::class);
        $this->contextProvider = $this->createMock(ContextProviderInterface::class);
        $this->dataProvider = $this->createMock(DataProviderInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->gridFactory = $this->createMock(GridFactoryInterface::class);
        $this->environment = $this->createMock(Environment::class);
        $this->controller = new Index(
            $this->uriVariablesExtractor,
            $this->contextProvider,
            $this->dataProvider,
            $this->formFactory,
            $this->gridFactory,
            $this->environment,
        );
    }
}
