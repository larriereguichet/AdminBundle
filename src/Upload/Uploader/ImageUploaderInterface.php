<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Upload\Uploader;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Image\ImageInterface;

interface ImageUploaderInterface
{
    /**
     * @param Collection<array-key, ImageInterface> $images
     */
    public function uploadImages(Collection $images): void;

    public function uploadImage(ImageInterface $image): void;

    public function removeImage(ImageInterface $image): void;
}
