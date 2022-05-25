<?php

namespace LAG\AdminBundle\Grid;

use LAG\AdminBundle\Field\Field;

class Row
{
    public function __construct(
        private int $index,
        private array $fields,
    ) {
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    /** @return Field[] */
    public function getFields(): array
    {
        return $this->fields;
    }
}
