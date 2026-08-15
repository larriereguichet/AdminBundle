<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Component;

use LAG\AdminBundle\Grid\View;

/**
 * Renders a grid as a table. Being a registered component rather than a bare template is what lets the grid html
 * attributes go through Symfony's ComponentAttributes: AttributeComponentRenderListener merges them into the
 * component attributes, so a template can apply its own defaults without losing what the grid declared.
 */
final class TableGrid implements AttributeComponentInterface
{
    public View\GridView $grid;
    public mixed $data;

    /** @return array<string, mixed> */
    public function getAttributes(): array
    {
        return $this->grid->attributes->all();
    }
}
