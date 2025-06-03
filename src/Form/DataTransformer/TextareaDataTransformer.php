<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

final readonly class TextareaDataTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        if ($value === null || !json_validate($value)) {
            return json_encode([['insert' => $value]]);
        }

        return $value;
    }

    public function reverseTransform(mixed $value): mixed
    {
        return $value;
    }
}
