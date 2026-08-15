<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Resource\Factory;

use LAG\AdminBundle\Cache\Resource\Factory\CacheResourceFactory;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

final class CacheResourceFactoryTest extends TestCase
{
    private CacheResourceFactory $factory;
    private MockObject $decorated;
    private MockObject $cache;

    #[Test]
    public function itCacheFactoryResults(): void
    {
        $expectedResource = new Resource(shortName: 'my_resource');

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with('my_resource')
            ->willReturnCallback(static fn (string $key, callable $callback) => $callback())
        ;
        $this->decorated
            ->expects($this->once())
            ->method('create')
            ->with('my_resource')
            ->willReturn($expectedResource)
        ;

        $resource = $this->factory->create('my_resource');

        self::assertSame($expectedResource, $resource);
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(ResourceFactoryInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);
        $this->factory = new CacheResourceFactory(
            $this->decorated,
            $this->cache,
        );
    }
}
