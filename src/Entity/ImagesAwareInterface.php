<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity;

use Doctrine\Common\Collections\Collection;

interface ImagesAwareInterface
{
    public function getImages(): Collection;

    public function setImages(Collection $images): void;

    public function addImage(ImageInterface $image): void;

    public function removeImage(ImageInterface $image): void;
}
