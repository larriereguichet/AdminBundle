<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Upload\Generator;

use LAG\AdminBundle\Entity\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function Symfony\Component\String\u;

class ImagePathGenerator implements ImagePathGeneratorInterface
{
    public function generatePath(ImageInterface $image): string
    {
        /** @var UploadedFile $file */
        $file = $image->getFile();

        $extension = u($file->getRealPath())->afterLast('.');
        $hash = bin2hex(random_bytes(16));
        $path = $hash.'.'.$extension;

        return \sprintf('%s/%s/%s', substr($path, 0, 2), substr($path, 2, 2), substr($path, 4));
    }
}
