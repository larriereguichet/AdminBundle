<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Upload\Uploader;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Image\ImageInterface;
use LAG\AdminBundle\Upload\Generator\ImagePathGeneratorInterface;
use League\Flysystem\FilesystemOperator;

final readonly class ImageUploader implements ImageUploaderInterface
{
    public function __construct(
        private FilesystemOperator $filesystem,
        private ImagePathGeneratorInterface $pathGenerator,
    ) {
    }

    public function uploadImages(Collection $images): void
    {
        foreach ($images as $image) {
            $this->uploadImage($image);
        }
    }

    public function uploadImage(ImageInterface $image): void
    {
        $file = $image->getFile();

        if ($file === null) {
            return;
        }
        $previousImage = null;

        if ($image->getPath() !== null && $this->filesystem->has($image->getPath())) {
            $previousImage = $image->getPath();
        }
        $path = $this->pathGenerator->generatePath($image);

        if (!file_exists($file->getRealPath())) {
            throw new Exception('The image "%s" file does not exists', $file->getRealPath());
        }
        $stream = fopen($file->getRealPath(), 'r');
        $this->filesystem->writeStream($path, $stream);

        if (\is_resource($stream)) {
            fclose($stream);
        }

        if ($previousImage !== null) {
            $this->filesystem->delete($previousImage);
        }
        $image->setPath($path);
    }

    public function removeImage(ImageInterface $image): void
    {
        if (!$this->filesystem->has($image->getPath())) {
            return;
        }
        $image->setPath(null);
        $image->setOwner(null);
        $this->filesystem->delete($image->getPath());
    }
}
