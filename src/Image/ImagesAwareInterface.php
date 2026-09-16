<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Image;

use Doctrine\Common\Collections\Collection;

/**
 * The contract is read only on purpose. An entity holding its own image class cannot declare
 * addImage(ItsOwnImage $image) against an addImage(ImageInterface $image) it inherits — narrowing a
 * parameter is illegal — so requiring the mutators here locked those entities out of the upload
 * listener, which only ever reads the collection. ImagesAwareTrait still provides them.
 */
interface ImagesAwareInterface
{
    /** @return Collection<int, ImageInterface> */
    public function getImages(): Collection;
}
