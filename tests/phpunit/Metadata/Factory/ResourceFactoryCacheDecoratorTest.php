<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryCacheDecorator;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Tests\TestCase;

class ResourceFactoryCacheDecoratorTest extends TestCase
{
    private ResourceFactoryCacheDecorator $decorator;
    private ResourceFactoryInterface $decorated;

    public function testCreate(): void
    {
        $definition = new AdminResource(name: 'my_resource');

        $this
            ->decorated
            ->expects($this->once())
            ->method('create')
        ;
        $this->decorator->create($definition);
        $this->decorator->create($definition);
    }

    public function testCreateWithDifferentName(): void
    {
        $this
            ->decorated
            ->expects($this->exactly(2))
            ->method('create')
        ;
        $this->decorator->create(new AdminResource(name: 'my_resource'));
        $this->decorator->create(new AdminResource(name: 'my_other_resource'));
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(ResourceFactoryInterface::class);
        $this->decorator = new ResourceFactoryCacheDecorator($this->decorated);
    }
}
