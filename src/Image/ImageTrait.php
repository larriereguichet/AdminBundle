<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Image;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

trait ImageTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $type = null;

    #[ORM\Column]
    private ?string $path = null;

    #[ORM\Column(nullable: true)]
    private ?string $name = null;

    private ?File $file = null;

    private ?object $owner = null;

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
