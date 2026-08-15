<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Bridge\KnpMenu\Extension;

use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Bridge\KnpMenu\Extension\ResourceExtension;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\OperationUrlGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ResourceExtensionTest extends TestCase
{
    private ResourceExtension $extension;
    private MockObject $operationFactory;
    private MockObject $urlGenerator;
    private MockObject $operationUrlGenerator;

    #[Test]
    public function itBuildOptions(): void
    {
        $this->urlGenerator->expects($this->never())->method('generateUrl');
        $operation = new Show(name: 'my_operation', route: 'my_route', title: 'Some title');
        $resource = new Resource(
            shortName: 'my_resource',
            operations: [$operation],
            translationDomain: 'my_domain',
        );
        $operation = $operation->setResource($resource);

        $this->operationFactory
            ->expects($this->once())
            ->method('create')
            ->with('my_operation')
            ->willReturn($operation)
        ;
        $this->operationUrlGenerator
            ->expects($this->once())
            ->method('generateUrl')
            ->with($operation)
            ->willReturn('/some-url')
        ;

        $options = [
            'some_option' => 'some_value',
            'resource' => 'my_resource',
            'operation' => 'my_operation',
        ];
        $buildOptions = $this->extension->buildOptions($options);

        self::assertEquals($options + [
            'uri' => '/some-url',
            'label' => 'Some title',
            'extras' => ['translation_domain' => 'my_domain'],
        ], $buildOptions);
    }

    #[Test]
    public function itBuildOptionsWithRouteParameters(): void
    {
        $operation = new Show(name: 'my_operation', route: 'my_route', title: 'Some title');
        $resource = new Resource(shortName: 'my_resource', operations: [$operation]);
        $operation = $operation->setResource($resource);

        $this->operationFactory->method('create')->willReturn($operation);
        $this->urlGenerator
            ->expects($this->once())
            ->method('generateUrl')
            ->with('my_route', ['id' => 1])
            ->willReturn('/some-url/1')
        ;
        $this->operationUrlGenerator->expects($this->never())->method('generateUrl');

        $options = $this->extension->buildOptions([
            'operation' => 'my_operation',
            'routeParameters' => ['id' => 1],
        ]);

        self::assertSame('/some-url/1', $options['uri']);
    }

    #[Test]
    public function itDoesNotBuildOptionsWithoutResource(): void
    {
        $this->operationFactory
            ->expects($this->never())
            ->method('create')
        ;
        $this->operationUrlGenerator
            ->expects($this->never())
            ->method('generateUrl')
        ;
        $this->urlGenerator
            ->expects($this->never())
            ->method('generateUrl')
        ;
        $options = ['some_option' => 'some_value'];
        $buildOptions = $this->extension->buildOptions($options);

        self::assertEquals($options, $buildOptions);
    }

    #[Test]
    public function itBuildItemDoesNothing(): void
    {
        $item = $this->createStub(ItemInterface::class);
        $this->extension->buildItem($item, []);
        $this->addToAssertionCount(1);
    }

    protected function setUp(): void
    {
        $this->operationFactory = $this->createMock(OperationFactoryInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->operationUrlGenerator = $this->createMock(OperationUrlGeneratorInterface::class);
        $this->extension = new ResourceExtension($this->operationFactory, $this->urlGenerator, $this->operationUrlGenerator);
    }
}
