<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Upload\Uploader;

use LAG\AdminBundle\Entity\ImageAwareInterface;
use LAG\AdminBundle\Entity\ImagesAwareInterface;

interface ImageUploaderInterface
{
    public function uploadImages(ImagesAwareInterface $owner): void;

    public function uploadImage(ImageAwareInterface $owner): void;

    public function removeImage(ImageAwareInterface $owner): void;
}
