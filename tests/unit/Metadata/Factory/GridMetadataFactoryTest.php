<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Exception\MissingMetadataException;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Factory\GridCollectionMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\GridMetadataFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GridMetadataFactoryTest extends TestCase
{
    #[Test]
    public function itCreateGridMetadata(): void
    {
        $collectionFactory = $this->createMock(GridCollectionMetadataFactoryInterface::class);
        $collectionFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn(['a_grid' => new Grid(name: 'a_grid')])
        ;
        $factory = new GridMetadataFactory($collectionFactory, ['table' => '@LAGAdmin/grid/table.html.twig']);
        $grid = $factory->createMetadata('a_grid');

        $this->assertInstanceOf(Grid::class, $grid);
        $this->assertEquals('a_grid', $grid->getName());
        $this->assertEquals('table', $grid->getType());
        $this->assertEquals('@LAGAdmin/grid/table.html.twig', $grid->getTemplate());
    }

    #[Test]
    public function itUsesExistingTemplateWhenGridHasOne(): void
    {
        $collectionFactory = $this->createMock(GridCollectionMetadataFactoryInterface::class);
        $collectionFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn(['a_grid' => new Grid(name: 'a_grid', template: '@App/my_grid.html.twig')])
        ;
        $factory = new GridMetadataFactory($collectionFactory, ['table' => '@LAGAdmin/grid/table.html.twig']);
        $grid = $factory->createMetadata('a_grid');

        $this->assertEquals('@App/my_grid.html.twig', $grid->getTemplate());
    }

    #[Test]
    public function itThrowsMissingMetadataException(): void
    {
        $collectionFactory = $this->createMock(GridCollectionMetadataFactoryInterface::class);
        $collectionFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn([])
        ;
        $factory = new GridMetadataFactory($collectionFactory, []);

        $this->expectException(MissingMetadataException::class);
        $factory->createMetadata('missing_grid');
    }
}
