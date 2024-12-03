<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Resource;

use LAG\AdminBundle\Controller\Resource\ResourceController;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\Entity\FakeEntity;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResourceControllerTest extends TestCase
{
    private ResourceController $controller;
    private MockObject $uriVariablesExtractor;
    private MockObject $contextProvider;
    private MockObject $provider;
    private MockObject $processor;
    private MockObject $formFactory;
    private MockObject $responseHandler;
    private MockObject $eventDispatcher;

    #[Test]
    public function itHandlesRequest(): void
    {
        $operation = new Index(
            name: 'my_operation',
            template: 'my.html.twig',
            normalizationContext: ['groups' => ['my_group']]
        );
        $resource = new Resource(
            name: 'my_resource',
            operations: [$operation],
            application: 'my_application',
        );
        $operation = $operation->withResource($resource);
        $request = new Request();
        $data = new FakeEntity();
        $data->id = 666;

        $this->uriVariablesExtractor
            ->expects(self::once())
            ->method('extractVariables')
            ->with($operation, $request)
            ->willReturn(['id' => 666, 'slug' => 'test'])
        ;
        $this->contextProvider
            ->expects(self::once())
            ->method('getContext')
            ->with($operation, $request)
            ->willReturn(['page' => 1])
        ;
        $this->provider
            ->expects(self::once())
            ->method('provide')
            ->with($operation, ['id' => 666, 'slug' => 'test'], ['page' => 1])
            ->willReturn($data)
        ;
        $this->eventDispatcher
            ->expects(self::once())
            ->method('dispatchEvents')
            ->with()
        ;
        $this->responseHandler
            ->expects(self::once())
            ->method('createResponse')
            ->willReturn(new Response(content: 'some html'))
        ;

        $response = $this->controller->__invoke($request, $operation);

        self::assertEquals('some html', $response->getContent());
    }

    #[Test]
    public function itHandlesRequestWithSubmittedForm(): void
    {
        $operation = new Index(
            name: 'my_operation',
            template: 'my.html.twig',
            route: 'my_route',
            form: FormType::class,
            formOptions: ['an_option' => 'a_value'],
        );
        $request = new Request();
        $data = new FakeEntity();
        $data->id = 666;
        $form = self::createMock(Form::class);

        $this
            ->uriVariablesExtractor
            ->expects(self::once())
            ->method('extractVariables')
            ->with($operation, $request)
            ->willReturn(['id' => 666, 'slug' => 'test'])
        ;
        $this
            ->contextProvider
            ->expects(self::once())
            ->method('getContext')
            ->with($operation, $request)
            ->willReturn(['page' => 1])
        ;
        $this
            ->provider
            ->expects(self::once())
            ->method('provide')
            ->with($operation, ['id' => 666, 'slug' => 'test'], ['page' => 1])
            ->willReturn($data)
        ;
        $this
            ->formFactory
            ->expects(self::once())
            ->method('create')
            ->with(FormType::class, $data, ['an_option' => 'a_value'])
            ->willReturn($form)
        ;

        $form
            ->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $form
            ->expects(self::once())
            ->method('isValid')
            ->willReturn(true)
        ;
        $form
            ->expects(self::once())
            ->method('getData')
            ->willReturn($data)
        ;

        $this
            ->processor
            ->expects(self::once())
            ->method('process')
            ->with($data, $operation, ['id' => 666, 'slug' => 'test'], ['page' => 1])
        ;

        $this
            ->responseHandler
            ->expects(self::once())
            ->method('createRedirectResponse')
            ->with($operation, $data, ['page' => 1])
            ->willReturn(new RedirectResponse('/url'))
        ;
        $this->responseHandler
            ->expects($this->never())
            ->method('createResponse')
        ;

        $response = $this->controller->__invoke($request, $operation);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/url', $response->getTargetUrl());
    }

    protected function setUp(): void
    {
        $this->uriVariablesExtractor = self::createMock(UriVariablesExtractorInterface::class);
        $this->contextProvider = self::createMock(ContextProviderInterface::class);
        $this->provider = self::createMock(ProviderInterface::class);
        $this->processor = self::createMock(ProcessorInterface::class);
        $this->formFactory = self::createMock(FormFactoryInterface::class);
        $this->responseHandler = self::createMock(ResponseHandlerInterface::class);
        $this->eventDispatcher = self::createMock(ResourceEventDispatcherInterface::class);
        $this->controller = new ResourceController(
            $this->uriVariablesExtractor,
            $this->contextProvider,
            $this->provider,
            $this->processor,
            $this->formFactory,
            $this->eventDispatcher,
            $this->responseHandler,
        );
    }
}
