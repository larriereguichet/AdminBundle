<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Resolver;

use LAG\AdminBundle\Resource\Locator\PropertyLocatorInterface;
use LAG\AdminBundle\Resource\Locator\ResourceLocatorInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Resolver\ClassResolverInterface;
use LAG\AdminBundle\Resource\Resolver\PhpFileResolverInterface;
use LAG\AdminBundle\Resource\Resolver\ResourceResolver;
use LAG\AdminBundle\Tests\Application\Entity\TestEntity;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

final class ResourceResolverTest extends TestCase
{
    private ResourceResolver $resolver;
    private MockObject $kernel;
    private MockObject $classResolver;
    private MockObject $resourceLocator;
    private MockObject $propertyLocator;
    private MockObject $phpFileResolver;

    #[Test]
    public function itResolveResources(): void
    {
        $directories = [
            __DIR__.'/../../../app/src',
            __DIR__.'/../../../app/config/admin/resources',
        ];

        $this->classResolver
            ->expects(self::exactly(2))
            ->method('resolveClass')
            ->willReturnMap([
                [realpath(__DIR__.'/../../../app/src/Entity/TestEntity.php'), new \ReflectionClass(TestEntity::class)],
                [realpath(__DIR__.'/../../../app/config/admin/resources/Project.php'), null],
            ])
        ;
        $this->resourceLocator
            ->expects(self::once())
            ->method('locateResources')
            ->with(new \ReflectionClass(TestEntity::class))
            ->willReturn([new Resource()])
        ;
        $this->propertyLocator
            ->expects(self::once())
            ->method('locateProperties')
            ->with(new \ReflectionClass(TestEntity::class))
            ->willReturn([])
        ;

        $resources = $this->resolver->resolveResources($directories);
        $resources = iterator_to_array($resources);
        $this->assertCount(1, $resources);
    }

    #[Test]
    public function itResolvesWithoutPath(): void
    {
        $this->resourceLocator
            ->expects($this->never())
            ->method('locateResources')
        ;
        $this->propertyLocator
            ->expects($this->never())
            ->method('locateProperties')
        ;

        $resources = iterator_to_array($this->resolver->resolveResources([]));
        $this->assertCount(0, $resources);
    }

    protected function setUp(): void
    {
        $this->kernel = self::createMock(KernelInterface::class);
        $this->classResolver = self::createMock(ClassResolverInterface::class);
        $this->resourceLocator = self::createMock(ResourceLocatorInterface::class);
        $this->propertyLocator = self::createMock(PropertyLocatorInterface::class);
        $this->phpFileResolver = self::createMock(PhpFileResolverInterface::class);
        $this->resolver = new ResourceResolver(
            $this->kernel,
            $this->classResolver,
            $this->resourceLocator,
            $this->propertyLocator,
            $this->phpFileResolver,
        );
    }
}
