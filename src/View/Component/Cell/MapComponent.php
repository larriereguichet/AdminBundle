<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component\Cell;

use LAG\AdminBundle\Metadata\Attribute\Map;
use LAG\AdminBundle\Grid\View\Cell;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:map',
    template: '@LAGAdmin/components/cells/map.html.twig',
)]
final class MapComponent
{
    public mixed $data;
    /** @var array<int|string, mixed> $map */
    public array $map = [];
    public ?string $mappedValue = null;
    public bool $translatable = false;
    public ?string $translationDomain = null;

    public function mount(mixed $data, Cell $cell): void
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
