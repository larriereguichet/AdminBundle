<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait ImageAwareTrait
{
    private ?ImageInterface $image = null;

    public function getImage(): ?ImageInterface
    {
        return $this->image;
    }

    public function setImage(?ImageInterface $image): void
    {
        $this->image = $image;
    }
}
