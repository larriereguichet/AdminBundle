<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Response\Handler;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Show;
use LAG\AdminBundle\Resource\Metadata\Update;
use LAG\AdminBundle\Response\Handler\ResponseHandler;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class ResponseHandlerTest extends TestCase
{
    private ResponseHandler $handler;
    private MockObject $environment;
    private MockObject $urlGenerator;

    #[Test]
    #[DataProvider(methodName: 'operations')]
    public function itCreatesAResponse(OperationInterface $operation, string $expectedResourceName): void
    {
        $request = new Request();
        $data = new \stdClass();

        $form = self::createMock(FormInterface::class);
        $formView = self::createMock(FormView::class);

        $filterForm = self::createMock(FormInterface::class);
        $filterFormView = self::createMock(FormView::class);

        $gridView = new GridView(
            name: 'my_grid',
            type: 'some_type',
            headers: [],
            rows: [],
        );

        $form->expects(self::once())
            ->method('createView')
            ->willReturn($formView)
        ;
        $filterForm->expects(self::once())
            ->method('createView')
            ->willReturn($filterFormView)
        ;

        $this->environment
            ->expects(self::once())
            ->method('render')
            ->with($operation->getTemplate(), [
                'resource' => $operation->getResource(),
                'operation' => $operation,
                'data' => $data,
                'form' => $formView,
                'filterForm' => $filterFormView,
                'grid' => $gridView,
                $expectedResourceName => $data,
            ])
        ;

        $this->handler->createCollectionResponse($request, $operation, $data, $form, $filterForm, $gridView);
    }

    #[Test]
    public function itCreatesARedirectionOperationResponse(): void
    {
        $operation = new Update(
            redirectApplication: 'my_other_application',
            redirectResource: 'my_other_resource',
            redirectOperation: 'my_other_operation'
        );
        $data = new \stdClass();
        $context = ['some' => 'context'];
        $url = '/some/url';

        $this->urlGenerator
            ->expects(self::once())
            ->method('generateFromOperationName')
            ->with('my_other_resource', 'my_other_operation', $data, 'my_other_application')
            ->willReturn($url)
        ;
        $this->urlGenerator
            ->expects(self::never())
            ->method('generateFromRouteName')
        ;
        $this->urlGenerator
            ->expects(self::never())
            ->method('generate')
        ;
        $response = $this->handler->createRedirectResponse($operation, $data, $context);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals($url, $response->getTargetUrl());
    }

    #[Test]
    public function itCreatesARedirectionRouteResponse(): void
    {
        $operation = new Update(
            redirectRoute: 'my_redirect_route',
            redirectRouteParameters: ['some' => 'parameter'],
        );
        $data = new \stdClass();
        $context = ['some' => 'context'];
        $url = '/some/url';

        $this->urlGenerator
            ->expects(self::once())
            ->method('generateFromRouteName')
            ->with($operation->getRedirectRoute(), $operation->getRedirectRouteParameters(), $data)
            ->willReturn($url)
        ;
        $this->urlGenerator
            ->expects(self::never())
            ->method('generateFromOperationName')
        ;
        $this->urlGenerator
            ->expects(self::never())
            ->method('generate')
        ;
        $response = $this->handler->createRedirectResponse($operation, $data, $context);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals($url, $response->getTargetUrl());
    }

    #[Test]
    public function itCreatesARedirectionOnSameRouteResponse(): void
    {
        $operation = new Update(
            route: 'my_redirect_route',
            routeParameters: ['some' => 'parameter'],
        );
        $data = new \stdClass();
        $context = ['some' => 'context'];
        $url = '/some/url';

        $this->urlGenerator
            ->expects(self::once())
            ->method('generateFromRouteName')
            ->with($operation->getRoute(), $operation->getRouteParameters(), $data)
            ->willReturn($url)
        ;
        $this->urlGenerator
            ->expects(self::never())
            ->method('generateFromOperationName')
        ;
        $this->urlGenerator
            ->expects(self::never())
            ->method('generate')
        ;
        $response = $this->handler->createRedirectResponse($operation, $data, $context);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals($url, $response->getTargetUrl());
    }

    public static function operations(): iterable
    {
        yield 'index' => [
            (new Index(template: 'some_template.html.twig'))->withResource(new Resource('article')),
            'articles',
        ];
        yield 'index_plural' => [
            (new Index(template: 'some_template.html.twig'))->withResource(new Resource('articles')),
            'articles',
        ];
        yield 'show' => [
            (new Show(template: 'some_template.html.twig'))->withResource(new Resource('article')),
            'article',
        ];
        yield 'show_plural' => [
            (new Show(template: 'some_template.html.twig'))->withResource(new Resource('articles')),
            'articles',
        ];
        yield 'update' => [
            (new Update(template: 'some_template.html.twig'))->withResource(new Resource('article')),
            'article',
        ];
        yield 'update_plural' => [
            (new Update(template: 'some_template.html.twig'))->withResource(new Resource('articles')),
            'articles',
        ];
        yield 'delete' => [
            (new Delete(template: 'some_template.html.twig'))->withResource(new Resource('article')),
            'article',
        ];
        yield 'delete_plural' => [
            (new Delete(template: 'some_template.html.twig'))->withResource(new Resource('articles')),
            'articles',
        ];
    }

    protected function setUp(): void
    {
        $this->environment = self::createMock(Environment::class);
        $this->urlGenerator = self::createMock(UrlGeneratorInterface::class);
        $this->handler = new ResponseHandler(
            $this->environment,
            $this->urlGenerator,
        );
    }
}
