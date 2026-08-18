<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Bridge\LiipImagine\DataTransformer\ImageDataTransformer;
use LAG\AdminBundle\Metadata\Attribute\Image;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ImageTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $image = new Image(name: 'cover');

        self::assertSame('cover', $image->getName());
        self::assertSame('@LAGAdmin/grids/properties/image.html.twig', $image->getTemplate());
        self::assertSame(ImageDataTransformer::class, $image->getDataTransformer());
        self::assertFalse($image->isSortable());
        self::assertNull($image->getImageFilter());
        self::assertNull($image->getStorage());
        self::assertTrue($image->getUpload());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $image = new Image(name: 'cover');

        $new = $image->withImageFilter('thumbnail_small');
        self::assertNotSame($image, $new);
        self::assertSame('thumbnail_small', $new->getImageFilter());
        self::assertNull($image->getImageFilter());

        $new = $image->withStorage('public');
        self::assertNotSame($image, $new);
        self::assertSame('public', $new->getStorage());
        self::assertNull($image->getStorage());

        $new = $image->withUpload(false);
        self::assertNotSame($image, $new);
        self::assertFalse($new->getUpload());
        self::assertTrue($image->getUpload());
    }
}
