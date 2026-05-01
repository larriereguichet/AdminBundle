<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Factory\GridCollectionAttributeMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\GridCollectionMetadataFactoryInterface;
use LAG\AdminBundle\Tests\Unit\ApplicationTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GridCollectionAttributeMetadataFactoryTest extends TestCase
{
    use ApplicationTestTrait;

    private GridCollectionAttributeMetadataFactory $factory;
    private MockObject $decorated;

    #[Test]
    public function itLoadsGridsFromClassAttributes(): void
    {
        $this->decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn([])
        ;
        $grids = $this->factory->createMetadata();

        self::assertArrayHasKey('projects_table', $grids);
        self::assertInstanceOf(Grid::class, $grids['projects_table']);
        self::assertSame('projects_table', $grids['projects_table']->getName());
    }

    #[Test]
    public function itMergesWithDecoratedFactoryResults(): void
    {
        $existingGrid = new Grid(name: 'existing_grid');
        $this->decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn(['existing_grid' => $existingGrid])
        ;
        $grids = $this->factory->createMetadata();

        self::assertArrayHasKey('existing_grid', $grids);
        self::assertArrayHasKey('projects_table', $grids);
    }

    #[Test]
    public function itOverridesDecoratedResultsWithAttributeGrids(): void
    {
        $overriddenGrid = new Grid(name: 'projects_table', title: 'Old Title');
        $this->decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn(['projects_table' => $overriddenGrid])
        ;
        $grids = $this->factory->createMetadata();

        self::assertSame('Books', $grids['projects_table']->getTitle());
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(GridCollectionMetadataFactoryInterface::class);
        $this->factory = new GridCollectionAttributeMetadataFactory(
            $this->decorated,
            [$this->getTestApplicationPath().'/src/Entity'],
        );
    }
}
