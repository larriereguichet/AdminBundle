<?php

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Metadata\Property\PropertyInterface;

readonly class CompositeDataTransformer implements PropertyDataTransformerInterface
{
    public function __construct(
        /** @var iterable<PropertyDataTransformerInterface> */
        private iterable $dataTransformers,
    )
    {
    }

    public function supports(PropertyInterface $property, mixed $data): bool
    {
        foreach ($this->dataTransformers as $transformer) {
            if ($transformer->supports($property, $data)) {
                return true;
            }
        }

        return false;
    }

    public function transform(PropertyInterface $property, mixed $data): mixed
    {
        foreach ($this->dataTransformers as $transformer) {
            if ($transformer->supports($property, $data)) {
                $data = $transformer->transform($property, $data);
            }
        }

        return $data;
    }
}
