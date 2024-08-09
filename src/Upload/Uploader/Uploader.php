<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Upload\Uploader;

use LAG\AdminBundle\Bridge\Flysystem\Registry\StorageRegistryInterface;
use LAG\AdminBundle\Entity\ImageInterface;
use LAG\AdminBundle\Upload\Generator\ImagePathGeneratorInterface;

final readonly class Uploader implements UploaderInterface
{
    public function __construct(
        private StorageRegistryInterface $registry,
        private ImagePathGeneratorInterface $pathGenerator,
    ) {
    }

    public function upload(ImageInterface $image, string $storageName = 'lag_admin_image.storage'): void
    {
        if (!$image->hasFile()) {
            return;
        }
        $filesystem = $this->registry->get($storageName);
        $file = $image->getFile();

        if ($image->getPath() !== null && $filesystem->has($image->getPath())) {
            $filesystem->delete($image->getPath());
        }
        $path = $this->pathGenerator->generatePath($image);
        $image->setPath($path);

        $filesystem->write($path, file_get_contents($file->getRealPath()));
    }

    public function remove(ImageInterface $image, string $storageName = 'lag_admin_image.storage'): void
    {
        $filesystem = $this->registry->get($storageName);

        if ($filesystem->has($image->getPath())) {
            $filesystem->delete($image->getPath());
            $image->setPath(null);
        }
    }
}
