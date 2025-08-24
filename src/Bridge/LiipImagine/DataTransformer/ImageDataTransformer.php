<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\LiipImagine\DataTransformer;

use LAG\AdminBundle\Entity\ImageInterface;
use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;
use LAG\AdminBundle\Metadata\Image;
use LAG\AdminBundle\Metadata\PropertyInterface;
use Liip\ImagineBundle\Templating\LazyFilterRuntime;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final readonly class ImageDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private LazyFilterRuntime $filterExtension,
    ) {
    }

    public function transform(PropertyInterface $property, mixed $data): ?string
    {
        if (!$property instanceof Image) {
            throw new UnexpectedTypeException($property, Image::class);
        }

        if (!$data instanceof ImageInterface) {
            return null;
        }

        return $this->filterExtension->filter($data->getPath(), $property->getImageFilter());
    }
}
