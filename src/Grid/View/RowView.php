<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use Symfony\UX\TwigComponent\ComponentAttributes;
use Traversable;

final readonly class RowView
{
    public ComponentAttributes $attributes;

    public function __construct(
        /** @var iterable<CellView> $cells */
        public iterable $cells = [],
        /** @var iterable<CellView> */
        public iterable $actions = [],
        array $attributes = [],
    ) {
        $this->attributes = new ComponentAttributes($attributes);
    }

    public function getIterator(): Traversable
    {
        foreach ($this->cells as $cell) {
            yield $cell;
        }
    }
}
