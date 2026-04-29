<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Image;

use Doctrine\Common\Collections\Collection;

interface ImagesAwareInterface
{
    /** @return Collection<int, ImageInterface> */
    public function getImages(): Collection;

    public function addImage(ImageInterface $image): void;

    public function removeImage(ImageInterface $image): void;
}
