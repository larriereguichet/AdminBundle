<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use Symfony\UX\TwigComponent\ComponentAttributes;

/**
 * @template-implements \IteratorAggregate<CellView>
 */
readonly class RowView implements \IteratorAggregate
{
    public function __construct(
        public ComponentAttributes $attributes,
        /** @var iterable<CellView|HeaderView> $cells */
        public iterable $cells = [],
        public mixed $data = null,
    ) {
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->cells as $cell) {
            yield $cell;
        }
    }
}
