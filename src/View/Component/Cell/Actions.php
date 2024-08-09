<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component\Cell;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:actions',
    template: '@LAGAdmin/components/cells/actions.html.twig',
)]
final class Actions
{
    public Collection $actions;

    public function __construct()
    {
        $this->actions = new ArrayCollection();
    }

    public function mount(iterable $data): void
    {
        /** @var \LAG\AdminBundle\Grid\View\CellView $item */
        foreach ($data as $item) {
            $this->actions->add([
                'data' => $item->data,
                'label' => $item->label,
                'attributes' => $item->attributes,
            ]);
        }
    }
}
