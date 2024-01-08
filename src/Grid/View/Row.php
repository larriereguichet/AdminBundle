<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use Symfony\Component\Form\FormView;

readonly class Row
{
    public function __construct(
        public int $index = 0,
        /** @var array<Cell> $cells */
        public array $cells = [],
        public array $attributes = [],
        public ?FormView $form = null,
    ) {
    }

    public function cell(string $name): ?Cell
    {
        return $this->cells[$name] ?? null;
    }
}
