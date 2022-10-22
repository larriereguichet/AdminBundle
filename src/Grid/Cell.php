<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid;

class Cell
{
    public function __construct(
        public readonly string $template,
        public readonly array $context = [],
        public readonly mixed $data = null,
    ) {
    }
}
