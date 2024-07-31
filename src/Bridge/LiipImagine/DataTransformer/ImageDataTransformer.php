<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\LiipImagine\DataTransformer;

use LAG\AdminBundle\Entity\ImageInterface;
use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;
use LAG\AdminBundle\Resource\Metadata\Image;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use Liip\ImagineBundle\Templating\LazyFilterRuntime;

final readonly class ImageDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private LazyFilterRuntime $filterExtension,
    ) {
    }

    /**
     * @param Image $property
     * @param ImageInterface $data
     */
    public function transform(PropertyInterface $property, mixed $data): ?string
    {
        if (!$data instanceof ImageInterface) {
            return null;
        }

        return $this->filterExtension->filter($data->getPath(), $property->getImageFilter());
    }
}
