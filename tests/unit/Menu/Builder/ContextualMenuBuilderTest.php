<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Menu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use LAG\AdminBundle\Bridge\KnpMenu\Builder\ContextualMenuBuilder;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

final class ContextualMenuBuilderTest extends TestCase
{
    private ContextualMenuBuilder $builder;
    private MockObject $resourceContext;
    private MockObject $operationFactory;
    private MockObject $routeNameGenerator;
    private MockObject $factory;

    #[Test]
    public function itBuildsContextualMenu(): void
    {
        $root = new MenuItem(name: 'root', factory: $this->factory); // @phpstan-ignore-line
        $item = new MenuItem(name: 'Some link', factory: $this->factory); // @phpstan-ignore-line

        $resource = new Resource();
        $operation = new Index(contextualLinks: [new Link(name: 'Some link', operation: 'admin.product.show', text: 'Some link')])->setResource($resource);
        $linkedOperation = new Show(name: 'show')->setResource($resource);

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(true)
        ;
        $this->resourceContext
            ->expects($this->once())
            ->method('getOperation')
            ->willReturn($operation)
        ;
        $this->operationFactory
            ->expects($this->once())
            ->method('create')
            ->with('admin.product.show')
            ->willReturn($linkedOperation)
        ;
        $this->routeNameGenerator
            ->expects($this->once())
            ->method('generateRouteName')
            ->with($resource, $linkedOperation)
            ->willReturn('some_route')
        ;
        $this->factory
            ->expects($this->exactly(2))
            ->method('createItem')
            ->willReturnMap([
                ['root', ['some_option' => 'some_value'], $root],
                ['Some link', ['route' => 'some_route'], $item],
            ])
        ;

        $this->builder->build(['some_option' => 'some_value']);
    }

    #[Test]
    public function itDoesNotBuildMenuWithoutOperation(): void
    {
        $root = new MenuItem(name: 'root', factory: $this->factory); // @phpstan-ignore-line

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(false)
        ;
        $this->resourceContext
            ->expects($this->never())
            ->method('getOperation')
        ;
        $this->operationFactory
            ->expects($this->never())
            ->method('create')
        ;
        $this->routeNameGenerator
            ->expects($this->never())
            ->method('generateRouteName')
        ;
        $this->factory
            ->expects($this->once())
            ->method('createItem')
            ->with('root', ['some_option' => 'some_value'])
            ->willReturn($root)
        ;

        $this->builder->build(['some_option' => 'some_value']);
    }

    #[Test]
    public function itBuildsMenuItemWithUrl(): void
    {
        $root = new MenuItem(name: 'root', factory: $this->factory); // @phpstan-ignore-line
        $item = new MenuItem(name: 'Some link', factory: $this->factory); // @phpstan-ignore-line

        $resource = new Resource();
        $operation = new Index(contextualLinks: [
            new Link(name: 'Some link', operation: 'admin.product.show', text: 'Some link', url: 'https://example.com'),
        ])->setResource($resource);
        $linkedOperation = new Show(name: 'show')->setResource($resource);

        $this->resourceContext->method('hasOperation')->willReturn(true);
        $this->resourceContext->method('getOperation')->willReturn($operation);
        $this->operationFactory->method('create')->willReturn($linkedOperation);
        $this->routeNameGenerator->expects($this->never())->method('generateRouteName');
        $this->factory->method('createItem')->willReturnMap([
            ['root', [], $root],
            ['Some link', ['url' => 'https://example.com'], $item],
        ]);

        $this->builder->build();
    }

    #[Test]
    public function itBuildsMenuItemWithRoute(): void
    {
        $root = new MenuItem(name: 'root', factory: $this->factory); // @phpstan-ignore-line
        $item = new MenuItem(name: 'Some link', factory: $this->factory); // @phpstan-ignore-line

        $resource = new Resource();
        $operation = new Index(contextualLinks: [
            new Link(name: 'Some link', operation: 'admin.product.show', text: 'Some link', route: 'my_route'),
        ])->setResource($resource);
        $linkedOperation = new Show(name: 'show')->setResource($resource);

        $this->resourceContext->method('hasOperation')->willReturn(true);
        $this->resourceContext->method('getOperation')->willReturn($operation);
        $this->operationFactory->method('create')->willReturn($linkedOperation);
        $this->routeNameGenerator->expects($this->never())->method('generateRouteName');
        $this->factory->method('createItem')->willReturnMap([
            ['root', [], $root],
            ['Some link', ['route' => 'my_route'], $item],
        ]);

        $this->builder->build();
    }

    #[Test]
    public function itBuildsMenuItemWithIcon(): void
    {
        $root = new MenuItem(name: 'root', factory: $this->factory); // @phpstan-ignore-line
        $item = new MenuItem(name: 'Create', factory: $this->factory); // @phpstan-ignore-line

        $resource = new Resource();
        $operation = new Index(contextualLinks: [
            new Link(name: 'Create', operation: 'create', text: 'Create', icon: 'custom-icon'),
        ])->setResource($resource);
        $linkedOperation = new Show(name: 'create')->setResource($resource);

        $this->resourceContext->method('hasOperation')->willReturn(true);
        $this->resourceContext->method('getOperation')->willReturn($operation);
        $this->operationFactory->method('create')->willReturn($linkedOperation);
        $this->routeNameGenerator->method('generateRouteName')->willReturn('some_route');
        $this->factory->method('createItem')->willReturnMap([
            ['root', [], $root],
            ['Create', ['route' => 'some_route', 'extras' => ['icon' => 'custom-icon']], $item],
        ]);

        $menu = $this->builder->build();

        self::assertSame($root, $menu);
    }

    #[Test]
    public function itBuildsMenuItemWithKnownOperationIcon(): void
    {
        $root = new MenuItem(name: 'root', factory: $this->factory); // @phpstan-ignore-line
        $item = new MenuItem(name: 'Create', factory: $this->factory); // @phpstan-ignore-line

        $resource = new Resource();
        $operation = new Index(contextualLinks: [
            new Link(name: 'Create', operation: 'create', text: 'Create'),
        ])->setResource($resource);
        $linkedOperation = new Show(name: 'create')->setResource($resource);

        $this->resourceContext->method('hasOperation')->willReturn(true);
        $this->resourceContext->method('getOperation')->willReturn($operation);
        $this->operationFactory->method('create')->willReturn($linkedOperation);
        $this->routeNameGenerator->method('generateRouteName')->willReturn('some_route');
        $this->factory->method('createItem')->willReturnMap([
            ['root', [], $root],
            ['Create', ['route' => 'some_route', 'extras' => ['icon' => 'plus-lg']], $item],
        ]);

        $menu = $this->builder->build();

        self::assertSame($root, $menu);
    }

    #[Test]
    public function itBuildsMenuItemWithUpdateIcon(): void
    {
        $root = new MenuItem(name: 'root', factory: $this->factory); // @phpstan-ignore-line
        $item = new MenuItem(name: 'Update', factory: $this->factory); // @phpstan-ignore-line

        $resource = new Resource();
        $operation = new Index(contextualLinks: [
            new Link(name: 'Update', operation: 'update', text: 'Update'),
        ])->setResource($resource);
        $linkedOperation = new Show(name: 'update')->setResource($resource);

        $this->resourceContext->method('hasOperation')->willReturn(true);
        $this->resourceContext->method('getOperation')->willReturn($operation);
        $this->operationFactory->method('create')->willReturn($linkedOperation);
        $this->routeNameGenerator->method('generateRouteName')->willReturn('some_route');
        $this->factory->method('createItem')->willReturnMap([
            ['root', [], $root],
            ['Update', ['route' => 'some_route', 'extras' => ['icon' => 'pencil-lg']], $item],
        ]);

        $menu = $this->builder->build();

        self::assertSame($root, $menu);
    }

    #[Test]
    public function itBuildsMenuItemWithDeleteIcon(): void
    {
        $root = new MenuItem(name: 'root', factory: $this->factory); // @phpstan-ignore-line
        $item = new MenuItem(name: 'Delete', factory: $this->factory); // @phpstan-ignore-line

        $resource = new Resource();
        $operation = new Index(contextualLinks: [
            new Link(name: 'Delete', operation: 'delete', text: 'Delete'),
        ])->setResource($resource);
        $linkedOperation = new Show(name: 'delete')->setResource($resource);

        $this->resourceContext->method('hasOperation')->willReturn(true);
        $this->resourceContext->method('getOperation')->willReturn($operation);
        $this->operationFactory->method('create')->willReturn($linkedOperation);
        $this->routeNameGenerator->method('generateRouteName')->willReturn('some_route');
        $this->factory->method('createItem')->willReturnMap([
            ['root', [], $root],
            ['Delete', ['route' => 'some_route', 'extras' => ['icon' => 'cross-lg']], $item],
        ]);

        $menu = $this->builder->build();

        self::assertSame($root, $menu);
    }

    #[Test]
    public function itBuildsMenuItemWithNoIconForUnknownOperation(): void
    {
        $root = new MenuItem(name: 'root', factory: $this->factory); // @phpstan-ignore-line
        $item = new MenuItem(name: 'Custom', factory: $this->factory); // @phpstan-ignore-line

        $resource = new Resource();
        $operation = new Index(contextualLinks: [
            new Link(name: 'Custom', operation: 'custom_op', text: 'Custom'),
        ])->setResource($resource);
        $linkedOperation = new Show(name: 'custom_op')->setResource($resource);

        $this->resourceContext->method('hasOperation')->willReturn(true);
        $this->resourceContext->method('getOperation')->willReturn($operation);
        $this->operationFactory->method('create')->willReturn($linkedOperation);
        $this->routeNameGenerator->method('generateRouteName')->willReturn('some_route');
        $this->factory->method('createItem')->willReturnMap([
            ['root', [], $root],
            ['Custom', ['route' => 'some_route'], $item],
        ]);

        $menu = $this->builder->build();

        self::assertSame($root, $menu);
    }

    protected function setUp(): void
    {
        $this->resourceContext = $this->createMock(ResourceContextInterface::class);
        $this->operationFactory = $this->createMock(OperationFactoryInterface::class);
        $this->routeNameGenerator = $this->createMock(RouteNameGeneratorInterface::class);
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->builder = new ContextualMenuBuilder(
            $this->resourceContext,
            $this->operationFactory,
            $this->routeNameGenerator,
            $this->factory,
        );
    }
}
