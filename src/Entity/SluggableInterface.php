<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity;

interface SluggableInterface
{
    public function getSlug(): ?string;

    public function getSlugSource(): ?string;

    public function generateSlug(string $source): void;
}
