<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Component;

use LAG\AdminBundle\Grid\View;

final class Grid implements TemplateComponentInterface, AttributeComponentInterface
{
    public View\GridView $grid;
    public mixed $data;

    public function getTemplate(): ?string
    {
        return $this->grid->template;
    }

    /** @return array<string, mixed> */
    public function getAttributes(): array
    {
        return $this->grid->attributes->all();
    }
}
