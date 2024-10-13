<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

// TODO remove
class ImageFileToArrayTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): array
    {
        return [
            'upload' => $value,
            'gallery' => $value,
        ];
    }

    public function reverseTransform(mixed $value): ?UploadedFile
    {
        if ($value['upload'] ?? false) {
            return $value['upload'];
        }

        if ($value['gallery'] ?? false) {
            return $value['gallery'];
        }

        return null;
    }
}
