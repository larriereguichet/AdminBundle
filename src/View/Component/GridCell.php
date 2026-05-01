<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\PropertyInterface;

final class GridCell
{
    public CellView $cell;
    public mixed $data;
    public PropertyInterface $property;

    /** @var array<string|mixed> */
    public array $context = [];

    public function mount(CellView $cell): void
    {
        $this->cell = $cell;
        $this->property = $cell->property;
        $this->context = $cell->context;
    }
}
