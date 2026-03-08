<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity;

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
