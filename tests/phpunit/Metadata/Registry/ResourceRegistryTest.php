<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\UnexpectedTypeException;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Metadata\Locator\MetadataLocatorInterface;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistry;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ResourceRegistryTest extends TestCase
{
    private ResourceRegistry $registry;
    private MockObject $locator;
    private MockObject $resourceFactory;

    public function testGet(): void
    {
        $resource1 = new AdminResource('my_resource');
        $resource2 = new AdminResource('my_other_resource');

        $this->locator
            ->expects($this->exactly(2))
            ->method('locateCollection')
            ->willReturnCallback(function (string $path) use ($resource1, $resource2) {
                $this->assertContains($path, ['/path/to', '/other']);

                if ($path === '/other') {
                    return [$resource1];
                }

                return [$resource2];
            })
        ;
        $this->resourceFactory
            ->expects($this->once())
            ->method('create')
            ->with($resource1)
            ->willReturn($resource1)
        ;

        $returnedResource = $this->registry->get('my_resource');

        $this->assertEquals($resource1, $returnedResource);
    }

    public function testGetTwice(): void
    {
        $resource1 = new AdminResource('my_resource');
        $resource2 = new AdminResource('my_other_resource');

        $this->locator
            ->expects($this->exactly(2))
            ->method('locateCollection')
            ->willReturnCallback(function (string $path) use ($resource1, $resource2) {
                $this->assertContains($path, ['/path/to', '/other']);

                return [$resource1, $resource2];
            })
        ;
        $this->resourceFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->with($resource1)
            ->willReturn($resource1)
        ;

        $this->registry->get('my_resource');
        $this->registry->get('my_resource');
    }

    public function testGetWithoutResources(): void
    {
        $this->locator
            ->expects($this->exactly(2))
            ->method('locateCollection')
            ->willReturn([new AdminResource('my_resource')])
        ;
        $this->resourceFactory
            ->expects($this->never())
            ->method('create')
        ;

        $this->expectException(Exception::class);
        $this->registry->get('wrong_resource');
    }

    public function testGetWrongResource(): void
    {
        $this->locator
            ->expects($this->exactly(2))
            ->method('locateCollection')
            ->willReturn([])
        ;
        $this->resourceFactory
            ->expects($this->never())
            ->method('create')
        ;

        $this->expectException(Exception::class);
        $this->registry->get('wrong_resource');
    }

    public function testHas(): void
    {
        $this->locator
            ->expects($this->exactly(2))
            ->method('locateCollection')
            ->willReturn([new AdminResource(name: 'my_resource'), new AdminResource(name: 'my_other_resource')])
        ;

        $this->assertTrue($this->registry->has('my_resource'));
        $this->assertTrue($this->registry->has('my_other_resource'));
        $this->assertFalse($this->registry->has('a_other_resource'));
        $this->assertFalse($this->registry->has('a_resource'));
        $this->assertFalse($this->registry->has(''));
    }

    public function testAll(): void
    {
        $resource1 = new AdminResource('my_resource');
        $resource2 = new AdminResource('my_other_resource');

        $this->locator
            ->expects($this->exactly(2))
            ->method('locateCollection')
            ->willReturnCallback(function (string $path) use ($resource1, $resource2) {
                $this->assertContains($path, ['/path/to', '/other']);

                return [$resource1, $resource2];
            })
        ;
        $this->resourceFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnCallback(function (AdminResource $resource) use ($resource1, $resource2) {
                if ($resource->getName() === $resource1->getName()) {
                    return $resource1;
                }

                if ($resource->getName() === $resource2->getName()) {
                    return $resource2;
                }

                $this->fail();
            })
        ;

        $returnedResources = $this->registry->all();

        $this->assertEquals([$resource1, $resource2], iterator_to_array($returnedResources));
    }

    public function testAllWithWrongResourceType(): void
    {
        $this->locator
            ->expects($this->exactly(1))
            ->method('locateCollection')
            ->willReturnCallback(fn () => [new \stdClass()])
        ;
        $this->resourceFactory
            ->expects($this->never())
            ->method('create')
        ;

        $this->expectException(UnexpectedTypeException::class);
        $returnedResources = $this->registry->all();
        iterator_to_array($returnedResources);
    }

    protected function setUp(): void
    {
        $this->locator = $this->createMock(MetadataLocatorInterface::class);
        $this->resourceFactory = $this->createMock(ResourceFactoryInterface::class);
        $this->registry = new ResourceRegistry(
            ['/path/to', '/other'],
            $this->locator,
            $this->resourceFactory,
        );
    }
}
