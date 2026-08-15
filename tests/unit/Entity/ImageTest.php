<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Entity;

use LAG\AdminBundle\Entity\Image;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

final class ImageTest extends TestCase
{
    #[Test]
    public function itHandlesAllProperties(): void
    {
        $image = new Image();

        self::assertNull($image->getId());
        self::assertNull($image->getFile());
        self::assertFalse($image->hasFile());
        self::assertNull($image->getType());
        self::assertNull($image->getPath());
        self::assertNull($image->getName());
        self::assertNull($image->getOwner());

        $image->setId(42);
        self::assertSame(42, $image->getId());

        $file = $this->createStub(File::class);
        $image->setFile($file);
        self::assertSame($file, $image->getFile());
        self::assertTrue($image->hasFile());

        $image->setFile(null);
        self::assertNull($image->getFile());
        self::assertFalse($image->hasFile());

        $image->setType('image/png');
        self::assertSame('image/png', $image->getType());

        $image->setPath('/uploads/image.png');
        self::assertSame('/uploads/image.png', $image->getPath());

        $image->setName('image.png');
        self::assertSame('image.png', $image->getName());

        $owner = new \stdClass();
        $image->setOwner($owner);
        self::assertSame($owner, $image->getOwner());

        $image->setOwner(null);
        self::assertNull($image->getOwner());
    }
}
