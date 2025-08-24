<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Menu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use LAG\AdminBundle\Menu\Builder\ContextualMenuBuilder;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Resource\Context\OperationContextInterface;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

final class ContextualMenuBuilderTest extends TestCase
{
    private ContextualMenuBuilder $builder;
    private MockObject $operationContext;
    private MockObject $operationFactory;
    private MockObject $routeNameGenerator;
    private MockObject $factory;

    #[Test]
    public function itBuildsContextualMenu(): void
    {
        $root = new MenuItem(name: 'root', factory: $this->factory); // @phpstan-ignore-line
        $item = new MenuItem(name: 'Some link', factory: $this->factory); // @phpstan-ignore-line

        $resource = new Resource();
        $operation = new Index(contextualActions: [new Link(operation: 'admin.product.show', text: 'Some link')])->setResource($resource);
        $linkedOperation = new Show(name: 'show')->setResource($resource);

        $this->operationContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(true)
        ;
        $this->operationContext
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

        $this->operationContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(false)
        ;
        $this->operationContext
            ->expects($this->never())
            ->method('getOperation')
        ;
        $this->factory
            ->expects($this->once())
            ->method('createItem')
            ->with('root', ['some_option' => 'some_value'])
            ->willReturn($root)
        ;

        $this->builder->build(['some_option' => 'some_value']);
    }

    protected function setUp(): void
    {
        $this->operationContext = $this->createMock(OperationContextInterface::class);
        $this->operationFactory = $this->createMock(OperationFactoryInterface::class);
        $this->routeNameGenerator = $this->createMock(RouteNameGeneratorInterface::class);
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->builder = new ContextualMenuBuilder(
            $this->operationContext,
            $this->operationFactory,
            $this->routeNameGenerator,
            $this->factory,
        );
    }
}
