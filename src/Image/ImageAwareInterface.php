<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Image;

interface ImageAwareInterface
{
    public function getImage(): ?ImageInterface;

    public function setImage(ImageInterface $images): void;
}
