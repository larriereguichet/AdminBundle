<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Upload\Uploader;

use LAG\AdminBundle\Entity\ImageInterface;

interface UploaderInterface
{
    public function upload(ImageInterface $image, string $storageName = 'lag_admin_image.storage'): void;

    public function remove(ImageInterface $image, string $storageName = 'lag_admin_image.storage'): void;
}
