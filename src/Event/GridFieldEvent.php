<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class GridFieldEvent extends Event
{
    public function __construct(
        private string $gridName,
        private array $fields = [],
    ) {
    }

    public function getGridName(): string
    {
        return $this->gridName;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }
}
