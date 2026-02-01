<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\Attribute\Application;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Resource;

interface DefinitionFactoryInterface
{
    public function createApplicationDefinition(string $applicationName): Application;

    public function createResourceDefinition(string $resourceName): Resource;

    public function createGridDefinition(string $gridName): Grid;

    /** @return array<int, string> */
    public function getResourceNames(): array;
}
