<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config;

use LAG\AdminBundle\Metadata\GridMetadataInterface;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;

final class LAGAdminBuilder
{
    /** @var array<string, ResourceMetadataInterface> */
    private array $resources = [];

    /** @var array<string, GridMetadataInterface> */
    private array $grids = [];

    public function __construct(
        private readonly string $env
    ) {
    }

    public function addResource(string $name, ResourceMetadataInterface $resource): self
    {
        $resource = $resource->withShortName($name);
        $this->resources[$resource->getName()] = $resource;

        return $this;
    }

    public function addGrid(string $name, GridMetadataInterface $grid): self
    {
        $grid = $grid->withName($name);
        $this->grids[$grid->getName()] = $grid;

        return $this;
    }

    /** @return array<string, ResourceMetadataInterface> */
    public function getResources(): array
    {
        return $this->resources;
    }

    /** @return array<string, GridMetadataInterface> */
    public function getGrids(): array
    {
        return $this->grids;
    }

    public function env(): string
    {
        return $this->env;
    }
}
