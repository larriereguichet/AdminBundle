<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Fixtures;

use LAG\AdminBundle\Entity\TimestampedResourceInterface;
use LAG\AdminBundle\Entity\TimestampResourceTrait;
use LAG\AdminBundle\Image\ImagesAwareInterface;
use LAG\AdminBundle\Image\ImagesAwareTrait;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Slug;
use LAG\AdminBundle\Metadata\Attribute\Text;

#[Resource(application: 'shop')]
#[Resource(application: 'admin')]
class Book implements TimestampedResourceInterface, ImagesAwareInterface
{
    use ImagesAwareTrait;
    use TimestampResourceTrait;

    #[Text]
    private ?int $id = null;

    #[Text]
    private ?string $name = null;

    #[Slug]
    private ?string $slug = null;

    private ?string $description = null;

    public function __construct()
    {
        $this->initializeImages();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }
}
