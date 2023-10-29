<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Transformer;

use LAG\AdminBundle\Entity\Image;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageFileToArrayTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): array
    {
        //        if ($value === null) {
        //            // return new Image();
        //        }
        //
        //        // return $value;
        //        if ($value === null) {
        //        }

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
