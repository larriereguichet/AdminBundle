<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config;

use LAG\AdminBundle\Metadata\Grid;

final class GridConfig implements GridConfigInterface
{
    /** @var Grid[] */
    private array $grids = [];

    public function addGrid(Grid $grid): GridConfigInterface
    {
        $this->grids[$grid->getName()] = $grid;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'lag_admin';
    }

    public function toArray(): array
    {
        $output = [];

        foreach ($this->grids as $grid) {
            $output['grids'][] = new ConfigurationMapper()->fromGrid($grid);
        }

        return $output;
    }
}
