<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

readonly class Header
{
    public function __construct(
        public string $name,
        public string $label,
        public bool $sortable,
        public ?string $translationDomain = null,
        public array $attributes = [],
    ) {
    }
}
