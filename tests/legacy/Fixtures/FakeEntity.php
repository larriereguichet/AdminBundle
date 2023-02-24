<?php

declare(strict_types=1);

namespace legacy\Fixtures;

class FakeEntity
{
    private ?string $id;
    private ?string $name;

    public function __construct(string $id = null, string $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
