<?php

namespace LAG\AdminBundle\View\Component\Cell;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Resource\Metadata\Map;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:map',
    template: '@LAGAdmin/components/cells/map.html.twig',
)]
final class MapComponent
{
    public mixed $data;
    public array $map = [];
    public ?string $mappedValue = null;
    public bool $translatable = false;
    public ?string $translationDomain = null;

    public function mount(mixed $data, CellView $cell): void
    {
        /** @var Map $property */
        $property = $cell->property;
        $this->data = $data;
        $this->map = $property->getMap();

        if (!empty($this->map[$data])) {
            $this->mappedValue = $this->map[$data];
        }
    }
}
