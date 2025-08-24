<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

final readonly class RowView implements \IteratorAggregate
{
    public function __construct(
        /** @var iterable<CellView|HeaderView> $cells */
        public iterable $cells = [],
        /** @var iterable<CellView> */
        public iterable $actions = [],
        public mixed $data = null,
        public array $attributes = [],
    ) {
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->cells as $cell) {
            yield $cell;
        }
    }
}
