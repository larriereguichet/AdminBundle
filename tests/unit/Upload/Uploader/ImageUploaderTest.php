<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Upload\Uploader;

use LAG\AdminBundle\Entity\Image;
use LAG\AdminBundle\Upload\Generator\ImagePathGeneratorInterface;
use LAG\AdminBundle\Upload\Uploader\ImageUploader;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

final class ImageUploaderTest extends TestCase
{
    private MockObject $filesystem;

    /** @var list<string> */
    private array $temporaryFiles = [];

    #[Test]
    public function itWritesTheFileContentToTheFilesystem(): void
    {
        $image = new Image();
        $image->setFile(new File($this->createTemporaryFile('the image content')));

        $this->filesystem
            ->expects($this->once())
            ->method('write')
            ->with('images/an-image.jpg', 'the image content')
        ;
        $this->filesystem
            ->expects($this->never())
            ->method('delete')
        ;

        $this->createUploader('images/an-image.jpg')->uploadImage($image);

        self::assertSame('images/an-image.jpg', $image->getPath());
    }

    #[Test]
    public function itDeletesThePreviousFile(): void
    {
        $image = new Image();
        $image->setPath('images/the-previous-image.jpg');
        $image->setFile(new File($this->createTemporaryFile('the new content')));

        $this->filesystem
            ->expects($this->once())
            ->method('has')
            ->with('images/the-previous-image.jpg')
            ->willReturn(true)
        ;
        $this->filesystem
            ->expects($this->once())
            ->method('delete')
            ->with('images/the-previous-image.jpg')
        ;

        $this->createUploader('images/the-new-image.jpg')->uploadImage($image);

        self::assertSame('images/the-new-image.jpg', $image->getPath());
    }

    #[Test]
    public function itDoesNotUploadAnImageWithoutFile(): void
    {
        $this->filesystem
            ->expects($this->never())
            ->method('write')
        ;

        $this->createUploader('images/an-image.jpg')->uploadImage(new Image());
    }

    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(FilesystemOperator::class);
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $this->temporaryFiles = [];
    }

    private function createUploader(string $generatedPath): ImageUploader
    {
        $pathGenerator = $this->createStub(ImagePathGeneratorInterface::class);
        $pathGenerator
            ->method('generatePath')
            ->willReturn($generatedPath)
        ;

        return new ImageUploader($this->filesystem, $pathGenerator);
    }

    private function createTemporaryFile(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'lag_admin_image_');

        if ($path === false) {
            self::fail('Unable to create a temporary file.');
        }
        file_put_contents($path, $content);
        $this->temporaryFiles[] = $path;

        return $path;
    }
}
