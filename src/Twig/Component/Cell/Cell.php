<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Component\Cell;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Twig\Component\AttributeComponentInterface;
use LAG\AdminBundle\Twig\Component\TemplateComponentInterface;

final class Cell implements TemplateComponentInterface, AttributeComponentInterface
{
    public CellView $cell;
    public mixed $data;
    public ?PropertyInterface $property = null;
    public ?string $component = null;

    /** @var array<string|mixed> */
    public array $context = [];

    public function mount(CellView $cell): void
    {
        $this->cell = $cell;
        $this->data = $cell->data;
        $this->property = $cell->property;
        $this->component = $cell->component;
        $this->context = $cell->context;
    }

    public function getTemplate(): ?string
    {
        return $this->cell->template;
    }

    /** @return array<string, mixed> */
    public function getAttributes(): array
    {
        return $this->property?->getAttributes() ?? [];
    }
}
