<?php

namespace LAG\AdminBundle\Grid;

class Row
{
    public function __construct(
        public readonly int $index,
        public readonly array $cells,
        public readonly mixed $data,
    ) {
    }
}
