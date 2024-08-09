<?php

namespace LAG\AdminBundle\Tests\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Menu\Builder\ContextualMenuBuilder;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Metadata\Action;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Link;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ContextualMenuBuilderTest extends TestCase
{
    private ContextualMenuBuilder $builder;
    private MockObject $resourceContext;
    private MockObject $registry;
    private MockObject $requestStack;
    private MockObject $routeNameGenerator;
    private MockObject $factory;

    #[Test]
    public function itBuildsContextualMenu(): void
    {
        $request = new Request();
        $root = self::createMock(ItemInterface::class);
        $operation = (new Index(
            contextualActions: [
                new Link(operation: 'index'),
            ],
        ))->withResource(new Resource());

        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;

        $this->factory
            ->expects(self::once())
            ->method('createItem')
            ->with('root', ['some_option' => 'some_value'])
            ->willReturn($root)
        ;

        $this->resourceContext
            ->expects(self::once())
            ->method('supports')
            ->with($request)
            ->willReturn(true)
        ;
        $this->resourceContext
            ->expects(self::once())
            ->method('getOperation')
            ->with($request)
            ->willReturn($operation)
        ;

        $this->builder->build(['some_option' => 'some_value']);
    }

    protected function setUp(): void
    {
        $this->resourceContext = self::createMock(ResourceContextInterface::class);
        $this->registry = self::createMock(ResourceRegistryInterface::class);
        $this->requestStack = self::createMock(RequestStack::class);
        $this->routeNameGenerator = self::createMock(RouteNameGeneratorInterface::class);
        $this->factory = self::createMock(FactoryInterface::class);
        $this->builder = new ContextualMenuBuilder(
            $this->resourceContext,
            $this->registry,
            $this->requestStack,
            $this->routeNameGenerator,
            $this->factory,
        );
    }
}
