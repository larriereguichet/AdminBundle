<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Resource;

use LAG\AdminBundle\Controller\Resource\ResourceController;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Response\Handler\RedirectHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\Entity\FakeEntity;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

final class ResourceControllerTest extends TestCase
{
    private ResourceController $controller;
    private UriVariablesExtractorInterface $uriVariablesExtractor;
    private MockObject $contextProvider;
    private MockObject $provider;
    private MockObject $processor;
    private MockObject $formFactory;
    private MockObject $environment;
    private MockObject $redirectHandler;
    private MockObject $serializer;
    private MockObject $eventDispatcher;

    #[Test]
    #[DataProvider(methodName: 'headers')]
    public function itHandlesRequest(array $headers, bool $useTwig): void
    {
        $operation = new Index(
            name: 'my_operation',
            template: 'my.html.twig',
            normalizationContext: ['groups' => ['my_group']]
        );
        $resource = new Resource(
            name: 'my_resource',
            application: 'my_application',
            operations: [$operation],
        );
        $operation = $operation->withResource($resource);
        $request = new Request(server: $headers);
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
        $expected = 'some html';

        if ($useTwig) {
            $this->environment
                ->expects(self::once())
                ->method('render')
                ->with('my.html.twig', [
                    'resource' => $resource,
                    'operation' => $operation,
                    'data' => $data,
                    'form' => null,
                ])
                ->willReturn('some html')
            ;

            $this->serializer
                ->expects($this->never())
                ->method('serialize')
            ;
        } else {
            $this->environment
                ->expects($this->never())
                ->method('render')
            ;

            $this->serializer
                ->expects(self::once())
                ->method('serialize')
                ->with($data, 'json', ['groups' => ['my_group']])
                ->willReturn('{"some": "json"}')
            ;
            $expected = '{"some": "json"}';
        }
        $response = $this->controller->__invoke($request, $operation);

        self::assertEquals($expected, $response->getContent());
    }

    #[Test]
    public function itHandlesRequestWithSubmittedForm(): void
    {
        $operation = new Index(
            name: 'my_operation',
            template: 'my.html.twig',
            form: FormType::class,
            formOptions: ['an_option' => 'a_value'],
            route: 'my_route',
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
            ->redirectHandler
            ->expects(self::once())
            ->method('createRedirectResponse')
            ->with($operation, $data, ['page' => 1])
            ->willReturn(new RedirectResponse('/url'))
        ;

        $this
            ->environment
            ->expects($this->never())
            ->method('render')
        ;

        $response = $this->controller->__invoke($request, $operation);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/url', $response->getTargetUrl());
    }

    public static function headers(): iterable
    {
        yield [['CONTENT_TYPE' => 'text/html'], true];
        yield [['CONTENT_TYPE' => 'application/json'], false];
    }

    protected function setUp(): void
    {
        $this->uriVariablesExtractor = self::createMock(UriVariablesExtractorInterface::class);
        $this->contextProvider = self::createMock(ContextProviderInterface::class);
        $this->provider = self::createMock(ProviderInterface::class);
        $this->processor = self::createMock(ProcessorInterface::class);
        $this->formFactory = self::createMock(FormFactoryInterface::class);
        $this->environment = self::createMock(Environment::class);
        $this->redirectHandler = self::createMock(RedirectHandlerInterface::class);
        $this->serializer = self::createMock(SerializerInterface::class);
        $this->eventDispatcher = self::createMock(ResourceEventDispatcherInterface::class);
        $this->controller = new ResourceController(
            $this->uriVariablesExtractor,
            $this->contextProvider,
            $this->provider,
            $this->processor,
            $this->formFactory,
            $this->environment,
            $this->redirectHandler,
            $this->serializer,
            $this->eventDispatcher,
        );
    }
}
