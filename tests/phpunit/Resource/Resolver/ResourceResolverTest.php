<?php

namespace LAG\AdminBundle\Tests\Resource\Resolver;

use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Resource\Locator\MetadataLocatorInterface;
use LAG\AdminBundle\Resource\Locator\ResourceLocatorInterface;
use LAG\AdminBundle\Resource\Resolver\ResourceResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ResourceResolverTest extends TestCase
{
    private ResourceResolver $resolver;
    private MockObject $resourceLocator;
    private MockObject $propertyLocator;

    public function testResolveResources(): void
    {
        $this->resourceLocator
            ->expects($this->exactly(2))
            ->method('locateResources')
            ->willReturnCallback(function (string $path) {
                $this->assertContains($path, ['/a/path', 'another/path']);

                if ($path === '/a/path') {
                    return [new Resource(name: 'MyResource', dataClass: 'MyResourceClass')];
                }

                return [];
            })
        ;
        $this->propertyLocator
            ->expects($this->once())
            ->method('locateMetadata')
            ->with('MyResourceClass')
            ->willReturn([])
        ;

        // iterator must be consumed to trigger calls
        $resources = iterator_to_array($this->resolver->resolveResources(['/a/path', 'another/path']));
        $this->assertCount(1, $resources);
    }

    public function testResolveWithoutPath(): void
    {
        $this->resourceLocator
            ->expects($this->never())
            ->method('locateResources')
        ;
        $this->propertyLocator
            ->expects($this->never())
            ->method('locateMetadata')
        ;

        $resources = iterator_to_array($this->resolver->resolveResources([]));
        $this->assertCount(0, $resources);
    }

    protected function setUp(): void
    {
        $this->resourceLocator = $this->createMock(ResourceLocatorInterface::class);
        $this->propertyLocator = $this->createMock(MetadataLocatorInterface::class);
        $this->resolver = new ResourceResolver(
            $this->resourceLocator,
            $this->propertyLocator,
        );
    }
}
