<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component\Cell;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Resource\Metadata\Date;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:date',
    template: '@LAGAdmin/components/cells/date.html.twig'
)]
final class DateComponent
{
    public \DateTimeInterface $data;
    public string $dateFormat;
    public ?string $timeFormat = null;
    public ?string $label = null;

    public function mount(mixed $data, CellView $cell): void
    {
        /** @var Date $property */
        $property = $cell->property;

        $this->dateFormat = $property->getDateFormat();
        $this->timeFormat = $property->getTimeFormat();
        $this->label = $property->getLabel();
        $this->data = $data;
    }
}
