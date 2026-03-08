<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Exception\MissingMetadataException;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Factory\GridMetadataFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GridMetadataFactoryTest extends TestCase
{
    private GridMetadataFactory $factory;

    #[Test]
    public function itCreateGridMetadata(): void
    {
        $grid = $this->factory->createMetadata('a_grid');

        $this->assertInstanceOf(Grid::class, $grid);
        $this->assertEquals('a_grid', $grid->getName());
    }

    #[Test]
    public function itThrowsMissingMetadataException(): void
    {
        $this->expectExceptionObject(new MissingMetadataException('Unable to find metadata fir the grid "missing_grid"'));
        $this->factory->createMetadata('missing_grid');
    }

    protected function setUp(): void
    {
        $this->factory = new GridMetadataFactory(
            ['a_grid' => ['class' => Grid::class, 'name' => 'a_grid']],
        );
    }
}
