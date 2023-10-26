<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid;

readonly class Cell
{
    public function __construct(
        public string $template,
        public array $context = [],
    ) {
    }
}
