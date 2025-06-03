<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent(
    name: 'lag_admin:grid',
    template: '@LAGAdmin/components/grids/grid.html.twig',
)]
final class Grid
{
    public array $titleAttributes = [];

    public array $headerRowAttributes = [];
    public array $headerAttributes = [];
    public array $rowAttributes = [];
    public array $cellAttributes = [];
    public array $actionCellAttributes = [];

    public mixed $data;
    public GridView $grid;
    public OperationInterface $operation;
    public Resource $resource;
    public array $options = [];

    public function mount(
        GridView $grid,
        mixed $data,
        array $options = [],
    ): void {
        $this->options = $grid->options + $options;
        $this->data = $data;
    }

    #[PostMount]
    public function postMount(): void
    {
        if ($this->grid->type === 'table') {
            $this->titleAttributes = array_merge(['class' => 'my-2 mb-5'], $this->titleAttributes);
        }
    }
}
