<?php

namespace LAG\AdminBundle\Grid;

class Grid
{
    public function __construct(
        private readonly string $name,
        private readonly array $headers,
        private readonly array $rows,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getRows(): array
    {
        return $this->rows;
    }
}
