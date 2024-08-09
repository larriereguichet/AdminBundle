<?php

namespace LAG\AdminBundle\View\Component\Grid;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\ComponentAttributes;

#[AsTwigComponent(
    name: 'lag_admin:grid',
    template: '@LAGAdmin/components/grids/grid.html.twig',
    exposePublicProps: true,
)]
class GridComponent
{
    public ComponentAttributes $headerRowAttributes;
    public ComponentAttributes $headerAttributes;
    public ComponentAttributes $rowAttributes;
    public ComponentAttributes $cellAttributes;
    public ComponentAttributes $titleAttributes;

    public mixed $data;
    public GridView $grid;
    public OperationInterface $operation;
    public Resource $resource;
    public array $options = [];

    public function mount(
        GridView $grid,
        OperationInterface $operation,
        mixed $data,
        array $options = [],
        array $headerRowAttributes = [],
        array $headerAttributes = [],
        array $rowAttributes = [],
        array $cellAttributes = [],
        array $titleAttributes = [],
    ): void {
        if ($grid->template === null && $grid->component === null) {
            throw new Exception(sprintf('The grid "%s" should have a template or a component', $grid->name));
        }
        $this->headerRowAttributes = new ComponentAttributes($grid->headerRowAttributes + $headerRowAttributes);
        $this->headerAttributes = new ComponentAttributes($grid->headerAttributes + $headerAttributes);
        $this->rowAttributes = new ComponentAttributes($grid->rowAttributes + $rowAttributes);
        $this->cellAttributes = new ComponentAttributes($grid->cellAttributes + $cellAttributes);
        $this->titleAttributes = new ComponentAttributes($grid->titleAttributes + $titleAttributes);

        $this->grid = $grid;
        $this->options = $grid->options + $options;
        $this->data = $data;
        $this->operation = $operation;
        $this->resource = $operation->getResource();
    }
}
