<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Fixtures;

use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Text;

#[Resource(application: 'shop')]
#[Resource(application: 'admin')]
#[Link(name: 'show_link')]
class Book
{
    #[Text]
    private ?int $id = null;

    #[Text]
    private ?string $name = null;

    private ?string $description = null;

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
}
