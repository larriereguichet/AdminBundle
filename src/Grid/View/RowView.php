<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use Symfony\UX\TwigComponent\ComponentAttributes;

final readonly class RowView implements \IteratorAggregate
{
    public ComponentAttributes $attributes;

    public function __construct(
        /** @var iterable<CellView|HeaderView> $cells */
        public iterable $cells = [],
        /** @var iterable<CellView> */
        public iterable $actions = [],
        public mixed $data = null,
        array $attributes = [],
    ) {
        $this->attributes = new ComponentAttributes($attributes);
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->cells as $cell) {
            yield $cell;
        }
    }
}
