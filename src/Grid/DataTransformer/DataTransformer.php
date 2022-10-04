<?php

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Metadata\Property\PropertyInterface;

class DataTransformer implements DataTransformerInterface
{
    public function __construct(
        /** @var iterable<DataTransformerInterface> $transformers */
        private iterable $transformers
    ) {
    }

    public function supports(PropertyInterface $property, mixed $data): bool
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->supports($property, $data)) {
                return true;
            }
        }

        return false;
    }

    public function transform(PropertyInterface $property, mixed $data): mixed
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->supports($property, $data)) {
                return $transformer->transform($property, $data);
            }
        }

        return $data;
    }
}
