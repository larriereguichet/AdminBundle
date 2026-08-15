<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Resource\Factory;

use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Factory\ResourceCollectionMetadataFactoryInterface;
use LAG\AdminBundle\Resource\Factory\ResourceCollectionFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ResourceCollectionFactoryTest extends TestCase
{
    private ResourceCollectionFactory $resourceCollectionFactory;
    private MockObject $resourceFactory;
    private MockObject $metadataCollectionFactory;

    #[Test]
    public function itCreatesAResourceCollection(): void
    {
        $resource1 = new Resource(shortName: 'a_resource');
        $resource2 = new Resource(shortName: 'another_resource');

        $this->metadataCollectionFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn([
                'a_resource' => $resource1,
                'another_resource' => $resource2,
            ])
        ;
        $this->resourceFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap([
                ['a_resource', $resource1],
                ['another_resource', $resource2],
            ])
        ;

        $resources = $this->resourceCollectionFactory->create();

        self::assertEquals(['a_resource' => $resource1, 'another_resource' => $resource2], $resources);
    }

    protected function setUp(): void
    {
        $this->metadataCollectionFactory = $this->createMock(ResourceCollectionMetadataFactoryInterface::class);
        $this->resourceFactory = $this->createMock(ResourceFactoryInterface::class);
        $this->resourceCollectionFactory = new ResourceCollectionFactory(
            $this->metadataCollectionFactory,
            $this->resourceFactory,
        );
    }
}
