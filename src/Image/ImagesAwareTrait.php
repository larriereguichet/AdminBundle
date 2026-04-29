<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Image;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait ImagesAwareTrait
{
    private Collection $images;

    protected function initializeImages(): void
    {
        $this->images = new ArrayCollection();
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ImageInterface $image): void
    {
        if (!$this->images->contains($image)) {
            $image->setOwner($this);
            $this->images->add($image);
        }
    }

    public function removeImage(ImageInterface $image): void
    {
        if ($this->images->contains($image)) {
            $image->setOwner(null);
            $this->images->removeElement($image);
        }
    }
}
