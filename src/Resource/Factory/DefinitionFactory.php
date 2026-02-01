<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Config\ConfigurationMapper;
use LAG\AdminBundle\Exception\MissingGridException;
use LAG\AdminBundle\Exception\Resource\MissingApplicationException;
use LAG\AdminBundle\Exception\Resource\MissingResourceException;
use LAG\AdminBundle\Metadata\Attribute\Application;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Resource\Locator\PropertyLocatorInterface;

final readonly class DefinitionFactory implements DefinitionFactoryInterface
{
    /**
     * @param array<string, array<string, mixed>> $applications
     * @param array<string, array<string, mixed>> $resources
     * @param array<string, array<string, mixed>> $grids
     */
    public function __construct(
        private array $applications,
        private array $resources,
        private array $grids,
        private PropertyLocatorInterface $propertyLocator,
        private ConfigurationMapper $configurationMapper = new ConfigurationMapper(),
    ) {
    }

    public function createApplicationDefinition(string $applicationName): Application
    {
        if (!\array_key_exists($applicationName, $this->applications)) {
            throw new MissingApplicationException($applicationName);
        }

        return $this->configurationMapper->toApplication($this->applications[$applicationName]);
    }

    public function createResourceDefinition(string $resourceName): Resource
    {
        if (!\array_key_exists($resourceName, $this->resources)) {
            throw new MissingResourceException($resourceName);
        }
        $definition = $this->configurationMapper->toResource($this->resources[$resourceName]);
        $properties = $this->propertyLocator->locateProperties($definition->getResourceClass());

        foreach ($properties as $property) {
            $definition = $definition->withProperty($property);
        }

        return $definition;
    }

    public function getResourceNames(): array
    {
        return array_keys($this->resources);
    }
}
