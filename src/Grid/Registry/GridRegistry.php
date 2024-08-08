<?php

namespace LAG\AdminBundle\Grid\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Grid\Builder\GridBuilderInterface;
use LAG\AdminBundle\Resource\Metadata\Grid;

final class GridRegistry implements GridRegistryInterface
{
    private array $grids = [];

    public function __construct(
        iterable $grids,
        /** @var iterable<GridBuilderInterface> $builders */
        private readonly iterable $builders,
    ) {
        foreach ($grids as $grid) {
            $this->grids[$grid->getName()] = $grid;
        }

        foreach ($this->builders as $builder) {
            $grid = $builder->build();
            $this->grids[$grid->getName()] = $grid;
        }
    }

    public function add(Grid $grid): void
    {
        if (!$grid->getName()) {
            throw new Exception('The grid has an empty name');
        }

        if ($this->has($grid->getName())) {
            throw new Exception(sprintf('The grid "%s" already exists', $grid->getName()));
        }
        $this->grids[$grid->getName()] = $grid;
    }

    public function get(string $gridName): Grid
    {
        if (!$this->has($gridName)) {
            throw new Exception(sprintf('The grid "%s" does not exists', $gridName));
        }

        return $this->grids[$gridName];
    }

    public function has(string $gridName): bool
    {
        return array_key_exists($gridName, $this->grids);
    }

    public function remove(string $gridName): void
    {
        unset($this->grids[$gridName]);
    }

    public function all(): iterable
    {
        return $this->grids;
    }
}
