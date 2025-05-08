<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\Resource;

interface DefinitionFactoryInterface
{
    public function createApplicationDefinition(string $applicationName): Application;

    public function createResourceDefinition(string $resourceName): Resource;

    public function createGridDefinition(string $gridName): Grid;

    public function getResourceNames(): array;
}
