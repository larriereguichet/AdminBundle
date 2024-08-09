<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Upload\Generator;

use LAG\AdminBundle\Entity\ImageInterface;

interface ImagePathGeneratorInterface
{
    /**
     * Generate a target path with subdirectories to the given image file.
     */
    public function generatePath(ImageInterface $image): string;
}
