<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\EventListener\Data;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Entity\Image;
use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\EventListener\Data\UploadImageListener;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Tests\Unit\DataProviderTestTrait;
use LAG\AdminBundle\Tests\Unit\Fixtures\Author;
use LAG\AdminBundle\Tests\Unit\Fixtures\Book;
use LAG\AdminBundle\Upload\Uploader\ImageUploaderInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UploadImageListenerTest extends TestCase
{
    use DataProviderTestTrait;

    private UploadImageListener $listener;
    private MockObject $uploader;

    #[Test]
    #[DataProvider('operations')]
    public function itUploadsImage(OperationInterface $operation): void
    {
        $book = new Book();
        $book->setImages(new ArrayCollection([new Image(), new Image()]));
        $event = new DataEvent($book, $operation);

        $this->uploader
            ->expects($this->once())
            ->method('uploadImages')
            ->with($book)
        ;

        $this->listener->__invoke($event);
    }

    #[Test]
    #[DataProvider('operations')]
    public function itUploadsOneImage(OperationInterface $operation): void
    {
        $author = new Author();
        $author->setImage(new Image());
        $event = new DataEvent($author, $operation);

        $this->uploader
            ->expects($this->once())
            ->method('uploadImage')
            ->with($author)
        ;

        $this->listener->__invoke($event);
    }

    protected function setUp(): void
    {
        $this->uploader = $this->createMock(ImageUploaderInterface::class);
        $this->listener = new UploadImageListener($this->uploader);
    }
}
