<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Data;

use LAG\AdminBundle\Entity\ImageInterface;
use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Resource\Metadata\Image;
use LAG\AdminBundle\Upload\Uploader\UploaderInterface;

final readonly class UploadListener
{
    public function __construct(
        private DataMapperInterface $dataMapper,
        private UploaderInterface $uploader,
    ) {
    }

    public function __invoke(DataEvent $event): void
    {
        $resource = $event->getResource();

        foreach ($resource->getProperties() as $property) {
            if ($property instanceof Image) {
                $images = $this->dataMapper->getValue($property, $event->getData());

                if (!is_iterable($images)) {
                    $images = [$images];
                }

                foreach ($images as $image) {
                    if (!$image) {
                        continue;
                    }

                    if (!$image instanceof ImageInterface) {
                        throw new Exception(sprintf(
                            'The image property "%s" expects an "%s", got "%s"',
                            $property->getName(),
                            ImageInterface::class,
                            is_object($image) ? get_class($image) : gettype($image),
                        ));
                    }

                    if ($image->hasFile()) {
                        if ($image->getPath()) {
                            $this->uploader->remove($image);
                        }
                        $this->uploader->upload($image);
                    }
                }
            }
        }
    }
}
