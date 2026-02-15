<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity;

use Doctrine\Common\Collections\Collection;

interface ImagesAwareInterface
{
    /** @return Collection<int, ImageInterface> */
    public function getImages(): Collection;

    /** @param Collection<int, ImageInterface> $images */
    public function setImages(Collection $images): void;

    public function addImage(ImageInterface $image): void;

    public function removeImage(ImageInterface $image): void;
}
