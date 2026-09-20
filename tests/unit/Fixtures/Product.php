<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Image\ImagesAwareInterface;

/**
 * An entity holding its own image class, which is what ImagesAwareTrait cannot express. The narrowed
 * addImage() below is the point of the fixture: putting addImage(ImageInterface $image) back on
 * ImagesAwareInterface makes this class a fatal error.
 */
class Product implements ImagesAwareInterface
{
    /** @var Collection<int, ProductImage> */
    private readonly Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /** @return Collection<int, ProductImage> */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ProductImage $image): void
    {
        if (!$this->images->contains($image)) {
            $image->setOwner($this);
            $this->images->add($image);
        }
    }
}
