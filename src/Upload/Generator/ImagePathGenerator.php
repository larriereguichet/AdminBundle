<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Upload\Generator;

use LAG\AdminBundle\Entity\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImagePathGenerator implements ImagePathGeneratorInterface
{
    public function generatePath(ImageInterface $image): string
    {
        /** @var UploadedFile $file */
        $file = $image->getFile();

        $hash = bin2hex(random_bytes(16));
        $path = $hash.'.'.$file->guessExtension();

        return \sprintf('%s/%s/%s', substr($path, 0, 2), substr($path, 2, 2), substr($path, 4));
    }
}
