<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use Traversable;

final readonly class GridView implements \IteratorAggregate
{
    public function __construct(
        public string $name,
        public string $type,
        public iterable $headers,
        public iterable $rows,
        public iterable $attributes = [],
        public ?string $title = null,
        public array $titleAttributes = [],
        public ?string $template = null,
        public ?string $component = null,
        public array $options = [],
        public array $actions = [],
        public array $context = [],
        public array $headerRowAttributes = [],
        public array $headerAttributes = [],
        public array $rowAttributes = [],
        public array $cellAttributes = [],
        public bool $extraColumn = false,
    ) {
    }

    public function getIterator(): Traversable
    {
        foreach ($this->rows as $row) {
            yield $row;
        }
    }
}
