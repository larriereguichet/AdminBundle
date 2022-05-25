<?php

namespace LAG\AdminBundle\Grid;

class Header
{
    public function __construct(
        private string $label,
        private bool $sortable,
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }
}
