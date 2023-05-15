<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller;

use LAG\AdminBundle\Controller\ItemOperationController;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\State\DataProcessorInterface;
use LAG\AdminBundle\State\DataProviderInterface;
use LAG\AdminBundle\Tests\Entity\FakeEntity;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class ItemOperationControllerTest extends TestCase
{
    private ItemOperationController $controller;
    private UriVariablesExtractorInterface $uriVariablesExtractor;
    private MockObject $contextProvider;
    private MockObject $dataProvider;
    private MockObject $dataProcessor;
    private MockObject $formFactory;
    private MockObject $environment;
    private MockObject $urlGenerator;

    public function testHandleRequest(): void
    {
        $operation = new Index(name: 'my_operation', template: 'my.html.twig');
        $resource = (new AdminResource())->withCurrentOperation($operation);
        $request = new Request();
        $data = new FakeEntity();
        $data->id = 666;

        $this
            ->uriVariablesExtractor
            ->expects($this->once())
            ->method('extractVariables')
            ->with($operation, $request)
            ->willReturn(['id' => 666, 'slug' => 'test'])
        ;
        $this
            ->contextProvider
            ->expects($this->once())
            ->method('getContext')
            ->with($operation, $request)
            ->willReturn(['page' => 1])
        ;
        $this
            ->dataProvider
            ->expects($this->once())
            ->method('provide')
            ->with($operation, ['id' => 666, 'slug' => 'test'], ['page' => 1])
            ->willReturn($data)
        ;

        $this
            ->environment
            ->expects($this->once())
            ->method('render')
            ->with('my.html.twig', [
                'resource' => $resource,
                'operation' => $operation,
                'data' => $data,
                'form' => null,
            ])
            ->willReturn('some html')
        ;

        $response = $this->controller->__invoke($request, $resource);

        $this->assertEquals('some html', $response->getContent());
    }

    public function testHandleRequestWithSubmittedForm(): void
    {
        $operation = new Index(
            name: 'my_operation',
            template: 'my.html.twig',
            formType: FormType::class,
            formOptions: ['an_option' => 'a_value'],
            route: 'my_route',
        );
        $resource = (new AdminResource())->withCurrentOperation($operation);
        $request = new Request();
        $data = new FakeEntity();
        $data->id = 666;
        $form = $this->createMock(Form::class);

        $this
            ->uriVariablesExtractor
            ->expects($this->once())
            ->method('extractVariables')
            ->with($operation, $request)
            ->willReturn(['id' => 666, 'slug' => 'test'])
        ;
        $this
            ->contextProvider
            ->expects($this->once())
            ->method('getContext')
            ->with($operation, $request)
            ->willReturn(['page' => 1])
        ;
        $this
            ->dataProvider
            ->expects($this->once())
            ->method('provide')
            ->with($operation, ['id' => 666, 'slug' => 'test'], ['page' => 1])
            ->willReturn($data)
        ;
        $this
            ->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(FormType::class, $data, ['an_option' => 'a_value'])
            ->willReturn($form)
        ;

        $form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;
        $form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data)
        ;

        $this
            ->dataProcessor
            ->expects($this->once())
            ->method('process')
            ->with($data, $operation, ['id' => 666, 'slug' => 'test'], ['page' => 1])
        ;

        $this
            ->urlGenerator
            ->expects($this->once())
            ->method('generateFromRouteName')
            ->with('my_route')
            ->willReturn('/url')
        ;

        $this
            ->environment
            ->expects($this->never())
            ->method('render')
        ;

        $response = $this->controller->__invoke($request, $resource);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/url', $response->getTargetUrl());
    }

    protected function setUp(): void
    {
        $this->uriVariablesExtractor = $this->createMock(UriVariablesExtractorInterface::class);
        $this->contextProvider = $this->createMock(ContextProviderInterface::class);
        $this->dataProvider = $this->createMock(DataProviderInterface::class);
        $this->dataProcessor = $this->createMock(DataProcessorInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->environment = $this->createMock(Environment::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->controller = new ItemOperationController(
            $this->uriVariablesExtractor,
            $this->contextProvider,
            $this->dataProvider,
            $this->dataProcessor,
            $this->formFactory,
            $this->environment,
            $this->urlGenerator,
        );
    }
}
