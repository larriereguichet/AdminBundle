<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use Symfony\UX\TwigComponent\ComponentAttributes;

readonly class HeaderView
{
    public ComponentAttributes $attributes;

    public function __construct(
        public string $name,
        public ?string $template = null,
        public ?string $label = null,
        public ?string $translationDomain = null,
        public ?string $sort = null,
        public ?string $order = null,
        public bool $sortable = false,
        array $attributes = [],
    ) {
        $this->attributes = new ComponentAttributes($attributes);
    }
}
