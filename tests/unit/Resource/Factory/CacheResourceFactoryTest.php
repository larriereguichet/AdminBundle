<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Factory;

use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Resource\Factory\CacheResourceFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CacheResourceFactoryTest extends TestCase
{
    private CacheResourceFactory $factory;
    private MockObject $decorated;

    #[Test]
    public function itCacheFactoryResults(): void
    {
        $expectedResource = new Resource(name: 'my_resource');

        $this->decorated
            ->expects(self::once())
            ->method('create')
            ->with('my_resource')
            ->willReturn($expectedResource)
        ;
        $this->factory->create('my_resource');
        $resource = $this->factory->create('my_resource');

        self::assertSame($expectedResource, $resource);
    }

    protected function setUp(): void
    {
        $this->decorated = self::createMock(ResourceFactoryInterface::class);
        $this->factory = new CacheResourceFactory(
            $this->decorated,
        );
    }
}
