<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use Symfony\UX\TwigComponent\ComponentAttributes;

/**
 * @implements \IteratorAggregate<int, Row>
 */
readonly class Grid implements \IteratorAggregate
{
    public function __construct(
        public string $name,
        public string $type,
        /** @var iterable<int, Row> $rows */
        public iterable $rows,
        public ComponentAttributes $attributes,
        public ?Row $headers = null,
        public ?Title $title = null,
        public ?string $template = null,
        /** @var array<string, mixed> $options */
        public array $options = [],
        /** @var array<int, Cell> $actions */
        public array $actions = [],
        /** @var array<string, mixed> $context */
        public array $context = [],
        public ?string $emptyMessage = null,
        public ?string $translationDomain = null,
    ) {
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->rows as $row) {
            yield $row;
        }
    }
}
