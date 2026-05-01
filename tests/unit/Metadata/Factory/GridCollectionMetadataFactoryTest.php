<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Factory\GridCollectionMetadataFactory;
use LAG\AdminBundle\Tests\Unit\ApplicationTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GridCollectionMetadataFactoryTest extends TestCase
{
    use ApplicationTestTrait;

    #[Test]
    public function itCreatesGridsFromConfigFiles(): void
    {
        $factory = new GridCollectionMetadataFactory(
            [$this->getTestApplicationPath().'/config/admin/grids'],
            'test',
        );
        $grids = $factory->createMetadata();

        self::assertArrayHasKey('publishers', $grids);
        self::assertInstanceOf(Grid::class, $grids['publishers']);
        self::assertSame('publishers', $grids['publishers']->getName());
        self::assertSame(['id', 'name'], $grids['publishers']->getProperties());
    }

    #[Test]
    public function itReturnsEmptyArrayWhenNoConfigFilesFound(): void
    {
        $tempDir = sys_get_temp_dir().'/lag_admin_test_'.uniqid();
        mkdir($tempDir);

        try {
            $factory = new GridCollectionMetadataFactory([$tempDir], 'test');
            $grids = $factory->createMetadata();
            self::assertSame([], $grids);
        } finally {
            rmdir($tempDir);
        }
    }
}
