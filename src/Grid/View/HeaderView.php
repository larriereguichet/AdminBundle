<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

readonly class HeaderView
{
    public function __construct(
        public string $name,
        public ?string $label = null,
        public bool $sortable = false,
        public ?string $translationDomain = null,
        public array $attributes = [],
    ) {
    }
}
