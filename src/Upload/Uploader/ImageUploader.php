<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Upload\Uploader;

use LAG\AdminBundle\Entity\ImageAwareInterface;
use LAG\AdminBundle\Entity\ImageInterface;
use LAG\AdminBundle\Entity\ImagesAwareInterface;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Upload\Generator\ImagePathGeneratorInterface;
use League\Flysystem\FilesystemOperator;

final readonly class ImageUploader implements ImageUploaderInterface
{
    public function uploadImages(ImagesAwareInterface $owner): void
    {
        foreach ($owner->getImages() as $image) {
            $this->uploadFile($image);
            $image->setOwner($owner);
        }
    }

    public function __construct(
        private FilesystemOperator $filesystem,
        private ImagePathGeneratorInterface $pathGenerator,
    ) {
    }

    public function uploadImage(ImageAwareInterface $owner): void
    {
        $image = $owner->getImage();

        if ($image === null) {
            return;
        }
        $this->uploadFile($image);
        $image->setOwner($owner);
    }

    public function removeImage(ImageAwareInterface $owner): void
    {
        $image = $owner->getImage();

        if ($image === null) {
            return;
        }

        if (!$this->filesystem->has($image->getPath())) {
            return;
        }
        $this->filesystem->delete($image->getPath());
        $image->setPath(null);
    }

    private function uploadFile(ImageInterface $image): void
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

        if (!$this->filesystem->fileExists($file->getRealPath())) {
            throw new Exception('The image "%s" file does not exists', $file->getRealPath());
        }
        $this->filesystem->copy($file->getRealPath(), $path);

        if ($previousImage !== null) {
            $this->filesystem->delete($previousImage);
        }
        $image->setPath($path);
    }
}
