<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid;

class Grid
{
    public function __construct(
        public readonly string $name,
        public readonly string $template,
        public readonly array $headers,
        public readonly array $rows,
    ) {
    }

    public function isEmpty(): bool
    {
        return \count($this->rows) === 0;
    }
}
