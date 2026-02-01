<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Data;

use LAG\AdminBundle\Entity\ImageAwareInterface;
use LAG\AdminBundle\Entity\ImagesAwareInterface;
use LAG\AdminBundle\Event\DataEvent;
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
            $this->uploader->uploadImage($data);
        }

        if ($data instanceof ImagesAwareInterface) {
            $this->uploader->uploadImages($data);
        }
    }
}
