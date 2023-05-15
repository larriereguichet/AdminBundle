<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid;

readonly class Grid
{
    public function __construct(
        public string $name,
        public string $template,
        public array $headers,
        public array $rows,
    ) {
    }

    public function isEmpty(): bool
    {
        return \count($this->rows) === 0;
    }
}
