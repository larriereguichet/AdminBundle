<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Factory\ResourceCollectionMetadataFactory;
use LAG\AdminBundle\Tests\Application\Entity\Publisher;
use LAG\AdminBundle\Tests\Unit\ApplicationTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResourceCollectionMetadataFactoryTest extends TestCase
{
    use ApplicationTestTrait;

    #[Test]
    public function itSkipsClassFiles(): void
    {
        $tempDir = sys_get_temp_dir().'/lag_admin_test_'.uniqid();
        mkdir($tempDir);
        file_put_contents($tempDir.'/entity.php', "<?php\n// return static function\nclass FakeResourceEntity {}\n");

        try {
            $factory = new ResourceCollectionMetadataFactory([$tempDir], 'test');
            $resources = $factory->createMetadata();
            self::assertSame([], $resources);
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
            $factory = new ResourceCollectionMetadataFactory([$tempDir], 'test');
            $resources = $factory->createMetadata();
            self::assertSame([], $resources);
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
            $factory = new ResourceCollectionMetadataFactory([$tempDir], 'test');
            $resources = $factory->createMetadata();
            self::assertSame([], $resources);
        } finally {
            unlink($tempDir.'/non_callable.php');
            rmdir($tempDir);
        }
    }

    #[Test]
    public function itCreatesResourceMetadataFromMappingDirectory(): void
    {
        $metadataFactory = new ResourceCollectionMetadataFactory([
            $this->getTestApplicationPath().'/config/admin',
            $this->getTestApplicationPath().'/src/Entity',
        ], 'test');
        $resources = $metadataFactory->createMetadata();

        self::assertEquals([
            'admin.publisher' => new Resource(
                shortName: 'publisher',
                application: 'admin',
                resourceClass: Publisher::class,
            ),
        ], $resources);
    }
}
