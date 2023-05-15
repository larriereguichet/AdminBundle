<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid;

readonly class Row
{
    public function __construct(
        public int $index,
        public array $cells,
        public mixed $data,
    ) {
    }
}
