<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Data;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\Image\ImageAwareInterface;
use LAG\AdminBundle\Image\ImagesAwareInterface;
use LAG\AdminBundle\Upload\Uploader\ImageUploaderInterface;

final readonly class UploadImageListener
{
    public function __construct(
        private ImageUploaderInterface $uploader,
    ) {
    }

    public function __invoke(DataEvent $event): void
    {
        $data = $event->getData();

        if ($data instanceof ImageAwareInterface) {
            $this->uploader->uploadImage($data->getImage());
        }

        if ($data instanceof ImagesAwareInterface) {
            $this->uploader->uploadImages($data->getImages());
        }
    }
}
