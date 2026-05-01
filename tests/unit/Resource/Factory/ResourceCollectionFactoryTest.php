<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Resource\Factory;

use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Resource\Factory\ResourceCollectionFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ResourceCollectionFactoryTest extends TestCase
{
    private ResourceCollectionFactory $resourceCollectionFactory;
    private MockObject $resourceFactory;

    #[Test]
    public function itCreatesAResourceCollection(): void
    {
        $resource1 = new Resource();
        $resource2 = new Resource();

        $this->resourceFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap([
                ['a_resource', $resource1],
                ['another_resource', $resource2],
            ])
        ;
        $resources = $this->resourceCollectionFactory->create();

        self::assertEquals([$resource1, $resource2], $resources);
    }

    protected function setUp(): void
    {
        $this->resourceFactory = $this->createMock(ResourceFactoryInterface::class);
        $this->resourceCollectionFactory = new ResourceCollectionFactory(
            [
                'a_resource' => ['name' => 'A Resource'],
                'another_resource' => ['name' => 'Another Resource'],
            ],
            $this->resourceFactory,
        );
    }
}
