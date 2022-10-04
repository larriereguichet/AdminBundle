<?php

namespace LAG\AdminBundle\Grid;

class Header
{
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly bool $sortable,
    ) {
    }
}
