<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Grid\Provider\GridProviderInterface;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Factory\GridMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\GridProviderMetadataFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GridProviderMetadataFactoryTest extends TestCase
{
    #[Test]
    public function itDelegatesToProviderWhenSupported(): void
    {
        $expectedGrid = new Grid(name: 'my_grid');

        $provider = $this->createMock(GridProviderInterface::class);
        $provider->method('supports')->with('my_grid')->willReturn(true);
        $provider->expects($this->once())->method('provide')->with('my_grid')->willReturn($expectedGrid);

        $decorated = $this->createMock(GridMetadataFactoryInterface::class);
        $decorated->expects($this->never())->method('createMetadata');

        $factory = new GridProviderMetadataFactory([$provider], $decorated);
        $result = $factory->createMetadata('my_grid');

        self::assertSame($expectedGrid, $result);
    }

    #[Test]
    public function itDelegatesToDecoratedFactoryWhenNoProviderSupports(): void
    {
        $expectedGrid = new Grid(name: 'my_grid');

        $provider = $this->createMock(GridProviderInterface::class);
        $provider->method('supports')->willReturn(false);
        $provider->expects($this->never())->method('provide');

        $decorated = $this->createMock(GridMetadataFactoryInterface::class);
        $decorated->expects($this->once())->method('createMetadata')->with('my_grid')->willReturn($expectedGrid);

        $factory = new GridProviderMetadataFactory([$provider], $decorated);
        $result = $factory->createMetadata('my_grid');

        self::assertSame($expectedGrid, $result);
    }

    #[Test]
    public function itDelegatesToDecoratedFactoryWhenNoProvidersRegistered(): void
    {
        $expectedGrid = new Grid(name: 'my_grid');

        $decorated = $this->createMock(GridMetadataFactoryInterface::class);
        $decorated->expects($this->once())->method('createMetadata')->with('my_grid')->willReturn($expectedGrid);

        $factory = new GridProviderMetadataFactory([], $decorated);
        $result = $factory->createMetadata('my_grid');

        self::assertSame($expectedGrid, $result);
    }
}
