<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Component;

use LAG\AdminBundle\Grid\View\RowView;

final class Row implements AttributeComponentInterface
{
    public RowView $row;

    /** @return array<string, mixed> */
    public function getAttributes(): array
    {
        return $this->row->attributes->all();
    }
}
