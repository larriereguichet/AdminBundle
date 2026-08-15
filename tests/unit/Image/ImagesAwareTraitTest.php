<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Image;

use LAG\AdminBundle\Entity\Image;
use LAG\AdminBundle\Tests\Unit\Fixtures\Book;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ImagesAwareTraitTest extends TestCase
{
    #[Test]
    public function itAddsAndRemovesImages(): void
    {
        $book = new Book();
        $image1 = new Image();
        $image2 = new Image();

        $book->addImage($image1);
        $book->addImage($image2);
        self::assertCount(2, $book->getImages());
        self::assertSame($book, $image1->getOwner());

        $book->addImage($image1);
        self::assertCount(2, $book->getImages());

        $book->removeImage($image1);
        self::assertCount(1, $book->getImages());
        self::assertNull($image1->getOwner());

        $book->removeImage($image1);
        self::assertCount(1, $book->getImages());
    }
}
