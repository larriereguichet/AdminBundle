<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;

interface ImageInterface
{
    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getFile(): ?File;

    public function setFile(?File $file): void;

    public function hasFile(): bool;

    public function getPath(): ?string;

    public function setPath(?string $path): void;
}
