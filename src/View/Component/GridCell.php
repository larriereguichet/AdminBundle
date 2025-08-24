<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\PropertyInterface;

final class GridCell implements DynamicTemplateComponentInterface
{
    public CellView $cell;
    public mixed $data;
    public ?PropertyInterface $options = null;
    public array $context;

    public function mount(CellView $cell): void
    {
        $this->cell = $cell;
        $this->options = $cell->options;
        $this->context = $cell->context;
    }

    public function getTemplate(): ?string
    {
        return $this->cell->template;
    }
}
