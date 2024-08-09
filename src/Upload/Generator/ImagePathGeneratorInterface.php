<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Upload\Generator;

use LAG\AdminBundle\Entity\ImageInterface;

interface ImagePathGeneratorInterface
{
    public function generatePath(ImageInterface $image): string;
}
