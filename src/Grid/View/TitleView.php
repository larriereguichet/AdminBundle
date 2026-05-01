<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use Symfony\UX\TwigComponent\ComponentAttributes;

readonly class TitleView
{
    public function __construct(
        public string $title,
        public ComponentAttributes $attributes,
        public string $translationDomain,
    ) {
    }
}
