<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Resource;

use LAG\AdminBundle\Controller\Resource\ResourceController;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Response\Handler\RedirectHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\Entity\FakeEntity;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

class ResourceControllerTest extends TestCase
{
    private ResourceController $controller;
    private UriVariablesExtractorInterface $uriVariablesExtractor;
    private MockObject $contextProvider;
    private MockObject $dataProvider;
    private MockObject $dataProcessor;
    private MockObject $formFactory;
    private MockObject $environment;
    private MockObject $redirectHandler;
    private MockObject $serializer;

    public static function headersProviders(): array
    {
        return [
            [['CONTENT_TYPE' => 'text/html'], true],
            [['CONTENT_TYPE' => 'application/json'], false],
        ];
    }

    /** @dataProvider headersProviders */
    public function testHandleRequest(array $headers, bool $useTwig): void
    {
        $operation = new Index(
            name: 'my_operation',
            template: 'my.html.twig',
            normalizationContext: ['groups' => ['my_group']]
        );
        $resource = new Resource(
            name: 'my_resource',
            operations: [$operation],
        );
        $operation = $operation->withResource($resource);
        $request = new Request(server: $headers);
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
        $expected = 'some html';

        if ($useTwig) {
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

            $this
                ->serializer
                ->expects($this->never())
                ->method('serialize')
            ;
        } else {
            $this
                ->environment
                ->expects($this->never())
                ->method('render')
            ;

            $this
                ->serializer
                ->expects($this->once())
                ->method('serialize')
                ->with($data, 'json', ['groups' => ['my_group']])
                ->willReturn('{"some": "json"}')
            ;
            $expected = '{"some": "json"}';
        }

        $response = $this->controller->__invoke($request, $operation);
        $this->assertEquals($expected, $response->getContent());
    }

    public function testHandleRequestWithSubmittedForm(): void
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
            ->redirectHandler
            ->expects($this->once())
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

    protected function setUp(): void
    {
        $this->uriVariablesExtractor = $this->createMock(UriVariablesExtractorInterface::class);
        $this->contextProvider = $this->createMock(ContextProviderInterface::class);
        $this->dataProvider = $this->createMock(ProviderInterface::class);
        $this->dataProcessor = $this->createMock(ProcessorInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->environment = $this->createMock(Environment::class);
        $this->redirectHandler = $this->createMock(RedirectHandlerInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->controller = new ResourceController(
            $this->uriVariablesExtractor,
            $this->contextProvider,
            $this->dataProvider,
            $this->dataProcessor,
            $this->formFactory,
            $this->environment,
            $this->redirectHandler,
            $this->serializer,
        );
    }
}
