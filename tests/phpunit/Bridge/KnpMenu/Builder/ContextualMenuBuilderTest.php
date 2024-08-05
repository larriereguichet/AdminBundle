<?php

namespace LAG\AdminBundle\Tests\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use LAG\AdminBundle\Menu\Builder\ContextualMenuBuilder;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ContextualMenuBuilderTest extends TestCase
{
    private ContextualMenuBuilder $builder;
    private MockObject $resourceContext;
    private MockObject $registry;
    private MockObject $requestStack;
    private MockObject $routeNameGenerator;
    private MockObject $factory;
    private MockObject $eventDispatcher;

    public function testBuildNonResourceMenu(): void
    {
        $this->builder->build();
    }

    protected function setUp(): void
    {
        $this->resourceContext = $this->createMock(ResourceContextInterface::class);
        $this->registry = $this->createMock(ResourceRegistryInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->routeNameGenerator = $this->createMock(RouteNameGeneratorInterface::class);
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->builder = new ContextualMenuBuilder(
            $this->resourceContext,
            $this->registry,
            $this->requestStack,
            $this->routeNameGenerator,
            $this->factory,
            $this->eventDispatcher,
        );
    }
}
