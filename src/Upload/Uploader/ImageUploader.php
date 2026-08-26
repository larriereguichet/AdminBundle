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

        // The path generator guesses the extension from the file itself, so the file has to be
        // checked before anything else reads it.
        if (!is_file($file->getPathname())) {
            throw new Exception('The image file "%s" does not exist or is not a regular file', $file->getPathname());
        }
        $previousImage = null;

        if ($image->getPath() !== null && $this->filesystem->has($image->getPath())) {
            $previousImage = $image->getPath();
        }
        $path = $this->pathGenerator->generatePath($image);
        $this->filesystem->write($path, $file->getContent());

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
