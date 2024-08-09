<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Flysystem\Registry;

use LAG\AdminBundle\Bridge\Flysystem\Registry\StorageRegistry;
use LAG\AdminBundle\Exception\Exception;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StorageRegistryTest extends TestCase
{
    #[Test]
    public function itReturnsAStorageByName(): void
    {
        $storage1 = self::createMock(FilesystemOperator::class);
        $storage2 = self::createMock(FilesystemOperator::class);

        $registry = new StorageRegistry(['my_storage' => $storage1, 'my_other_storage' => $storage2]);

        self::assertEquals($storage1, $registry->get('my_storage'));
        self::assertTrue($registry->has('my_storage'));
        self::assertEquals($storage1, $registry->get('my_other_storage'));
        self::assertTrue($registry->has('my_other_storage'));
    }

    #[Test]
    public function itThrowsAnExceptionWhenTheStorageDoesNotExist(): void
    {
        $storage1 = self::createMock(FilesystemOperator::class);
        $storage2 = self::createMock(FilesystemOperator::class);

        $registry = new StorageRegistry(['my_storage' => $storage1, 'my_other_storage' => $storage2]);

        self::expectExceptionObject(new Exception('The storage "some_storage" does not exist.'));
        self::assertFalse($registry->has('some_storage'));
        $registry->get('some_storage');
    }
}
