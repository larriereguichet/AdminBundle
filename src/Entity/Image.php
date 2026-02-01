<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

class Image implements ImageInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column]
    private ?int $id = null;

    private ?File $file = null;

    #[ORM\Column(nullable: true)]
    private ?string $type = null;

    #[ORM\Column]
    private ?string $path = null;

    #[ORM\Column(nullable: true)]
    private ?string $name = null;

    private ?object $owner = null;

    public function __toString(): string
    {
        return $this->path ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): void
    {
        $this->file = $file;
    }

    public function hasFile(): bool
    {
        return $this->file !== null;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getOwner(): ?object
    {
        return $this->owner;
    }

    public function setOwner(?object $owner): void
    {
        $this->owner = $owner;
    }
}
