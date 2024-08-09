<?php

namespace LAG\AdminBundle\Tests\Resource\Resolver;

use LAG\AdminBundle\Resource\Locator\MetadataLocatorInterface;
use LAG\AdminBundle\Resource\Locator\PropertyLocatorInterface;
use LAG\AdminBundle\Resource\Locator\ResourceLocatorInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Resolver\ClassResolverInterface;
use LAG\AdminBundle\Resource\Resolver\ResourceResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ResourceResolverTest extends TestCase
{
    private ResourceResolver $resolver;
    private MockObject $classResolver;
    private MockObject $resourceLocator;
    private MockObject $propertyLocator;

    public function loltestResolveResources(): void
    {
        $this->resourceLocator
            ->expects(self::exactly(2))
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
            ->expects(self::once())
            ->method('locateMetadata')
            ->with('MyResourceClass')
            ->willReturn([])
        ;

        // iterator must be consumed to trigger calls
        $resources = iterator_to_array($this->resolver->resolveResources(['/a/path', 'another/path']));
        $this->assertCount(1, $resources);
    }

    public function loltestResolveWithoutPath(): void
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
        $this->classResolver = self::createMock(ClassResolverInterface::class);
        $this->resourceLocator = self::createMock(ResourceLocatorInterface::class);
        $this->propertyLocator = self::createMock(PropertyLocatorInterface::class);
        $this->resolver = new ResourceResolver(
            $this->classResolver,
            $this->resourceLocator,
            $this->propertyLocator,
        );
    }
}
