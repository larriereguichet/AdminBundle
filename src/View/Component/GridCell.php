<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Grid\View\Cell;

final class GridCell
{
    public Cell $cell;
    public mixed $data;
    public PropertyInterface $property;

    /** @var array<string|mixed> $context */
    public array $context = [];

    public function mount(Cell $cell): void
    {
        $this->cell = $cell;
        $this->property = $cell->property;
        $this->context = $cell->context;
    }
}
