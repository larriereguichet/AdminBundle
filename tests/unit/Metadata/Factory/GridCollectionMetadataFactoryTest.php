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

    #[Test]
    public function itReturnsCachedResultOnSecondCall(): void
    {
        $factory = new GridCollectionMetadataFactory(
            [$this->getTestApplicationPath().'/config/admin/grids'],
            'test',
        );

        $first = $factory->createMetadata();
        $second = $factory->createMetadata();

        self::assertSame($first, $second);
    }

    #[Test]
    public function itSkipsClassFiles(): void
    {
        $tempDir = sys_get_temp_dir().'/lag_admin_test_'.uniqid();
        mkdir($tempDir);
        file_put_contents($tempDir.'/entity.php', "<?php\n// return static function\nclass FakeGridEntity {}\n");

        try {
            $factory = new GridCollectionMetadataFactory([$tempDir], 'test');
            $grids = $factory->createMetadata();
            self::assertSame([], $grids);
        } finally {
            unlink($tempDir.'/entity.php');
            rmdir($tempDir);
        }
    }

    #[Test]
    public function itSkipsFilesThrowingDuringInclude(): void
    {
        $tempDir = sys_get_temp_dir().'/lag_admin_test_'.uniqid();
        mkdir($tempDir);
        file_put_contents($tempDir.'/broken.php', "<?php\n// return static function\nthrow new \\RuntimeException('broken');\n");

        try {
            $factory = new GridCollectionMetadataFactory([$tempDir], 'test');
            $grids = $factory->createMetadata();
            self::assertSame([], $grids);
        } finally {
            unlink($tempDir.'/broken.php');
            rmdir($tempDir);
        }
    }

    #[Test]
    public function itSkipsFilesReturningNonCallable(): void
    {
        $tempDir = sys_get_temp_dir().'/lag_admin_test_'.uniqid();
        mkdir($tempDir);
        file_put_contents($tempDir.'/non_callable.php', "<?php\n// return static function\nreturn 42;\n");

        try {
            $factory = new GridCollectionMetadataFactory([$tempDir], 'test');
            $grids = $factory->createMetadata();
            self::assertSame([], $grids);
        } finally {
            unlink($tempDir.'/non_callable.php');
            rmdir($tempDir);
        }
    }
}
