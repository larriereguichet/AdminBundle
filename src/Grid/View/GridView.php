<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use Symfony\UX\TwigComponent\ComponentAttributes;

/**
 * @implements \IteratorAggregate<int, RowView>
 */
readonly class GridView implements \IteratorAggregate
{
    public function __construct(
        public string $name,
        public string $type,
        /** @var iterable<int, RowView> $rows */
        public iterable $rows,
        public ComponentAttributes $attributes,
        public ?RowView $headers = null,
        public ?TitleView $title = null,
        public ?string $template = null,
        /** @var array<string, mixed> $options */
        public array $options = [],
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
