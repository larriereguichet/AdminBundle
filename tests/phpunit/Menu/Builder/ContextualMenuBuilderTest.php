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
        $root = new MenuItem(name: 'root', factory: $this->factory);
        $item = new MenuItem(name: 'Some link', factory: $this->factory);

        $resource = new Resource();
        $operation = new Index(contextualActions: [new Link(operation: 'admin.product.show', text: 'Some link')])->withResource($resource);
        $linkedOperation = new Show(shortName: 'show')->withResource($resource);

        $this->operationContext
            ->expects(self::once())
            ->method('hasOperation')
            ->willReturn(true)
        ;
        $this->operationContext
            ->expects(self::once())
            ->method('getOperation')
            ->willReturn($operation)
        ;
        $this->operationFactory
            ->expects(self::once())
            ->method('create')
            ->with('admin.product.show')
            ->willReturn($linkedOperation)
        ;
        $this->routeNameGenerator
            ->expects(self::once())
            ->method('generateRouteName')
            ->with($resource, $linkedOperation)
            ->willReturn('some_route')
        ;
        $this->factory
            ->expects(self::exactly(2))
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
        $root = new MenuItem(name: 'root', factory: $this->factory);

        $this->operationContext
            ->expects(self::once())
            ->method('hasOperation')
            ->willReturn(false)
        ;
        $this->operationContext
            ->expects(self::never())
            ->method('getOperation')
        ;
        $this->factory
            ->expects(self::once())
            ->method('createItem')
            ->with('root', ['some_option' => 'some_value'])
            ->willReturn($root)
        ;

        $this->builder->build(['some_option' => 'some_value']);
    }

    protected function setUp(): void
    {
        $this->operationContext = self::createMock(OperationContextInterface::class);
        $this->operationFactory = self::createMock(OperationFactoryInterface::class);
        $this->routeNameGenerator = self::createMock(RouteNameGeneratorInterface::class);
        $this->factory = self::createMock(FactoryInterface::class);
        $this->builder = new ContextualMenuBuilder(
            $this->operationContext,
            $this->operationFactory,
            $this->routeNameGenerator,
            $this->factory,
        );
    }
}
