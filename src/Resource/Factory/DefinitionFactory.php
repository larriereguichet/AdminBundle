<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Config\Mapper\ApplicationMapper;
use LAG\AdminBundle\Config\Mapper\GridMapper;
use LAG\AdminBundle\Config\Mapper\ResourceMapper;
use LAG\AdminBundle\Exception\MissingGridException;
use LAG\AdminBundle\Exception\Resource\MissingApplicationException;
use LAG\AdminBundle\Exception\Resource\MissingResourceException;
use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Resource\Locator\PropertyLocatorInterface;

final readonly class DefinitionFactory implements DefinitionFactoryInterface
{
    public function __construct(
        private array $applications,
        private array $resources,
        private array $grids,
        private PropertyLocatorInterface $propertyLocator,
    ) {
    }

    public function createApplicationDefinition(string $applicationName): Application
    {
        if (!\array_key_exists($applicationName, $this->applications)) {
            throw new MissingApplicationException($applicationName);
        }

        return new ApplicationMapper()->fromArray($this->applications[$applicationName]);
    }

    public function createResourceDefinition(string $resourceName): Resource
    {
        if (!\array_key_exists($resourceName, $this->resources)) {
            throw new MissingResourceException($resourceName);
        }
        $definition = new ResourceMapper()->fromArray($this->resources[$resourceName]);
        $properties = $this->propertyLocator->locateProperties($definition->getResourceClass());

        foreach ($properties as $property) {
            $definition = $definition->withProperty($property);
        }

        return $definition;
    }

    public function createGridDefinition(string $gridName): Grid
    {
        if (!\array_key_exists($gridName, $this->grids)) {
            throw new MissingGridException($gridName);
        }

        return new GridMapper()->fromArray($this->grids[$gridName]);
    }

    public function getResourceNames(): array
    {
        return array_keys($this->resources);
    }
}
