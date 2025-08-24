<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Text;
use LAG\AdminBundle\Resource\Factory\OperationFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

final class OperationFactoryTest extends TestCase
{
    private OperationFactory $factory;
    private MockObject $resourceFactory;

    #[Test]
    public function itCreatesAnOperation(): void
    {
        $resource = new Resource(
            name: 'my_resource',
            properties: [new Text('my_property')],
            operations: [
                new Index(),
            ]
        );

        $this->resourceFactory
            ->expects($this->once())
            ->method('create')
            ->with('admin.book')
            ->willReturn($resource)
        ;

        $operation = $this->factory->create('admin.book.index');

        self::assertEquals($resource->getOperation('index'), $operation);
    }

    protected function setUp(): void
    {
        $this->resourceFactory = $this->createMock(ResourceFactoryInterface::class);
        $this->factory = new OperationFactory(
            $this->resourceFactory,
        );
    }
}
