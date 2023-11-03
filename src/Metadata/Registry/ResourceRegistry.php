<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\UnexpectedTypeException;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Metadata\Locator\MetadataLocatorInterface;

class ResourceRegistry implements ResourceRegistryInterface
{
    private array $resources = [];

    public function __construct(
        /** @var iterable<AdminResource> $resources */
        iterable $resources,
        private string $defaultApplicationName,
    ) {
        foreach ($resources as $resource) {
            $this->resources[$resource->getApplicationName()][$resource->getName()] = $resource;
        }
    }

    public function get(string $resourceName, ?string $applicationName = null): AdminResource
    {
        $applicationName = $applicationName ?? $this->defaultApplicationName;

        if (!$this->has($resourceName, $applicationName)) {
            throw new Exception('Resource with name "'.$resourceName.'" not found');
        }

        return $this->resources[$applicationName][$resourceName];
    }

    public function all(): iterable
    {
        foreach ($this->resources as $resources) {
            foreach ($resources as $resource) {
                yield $resource;
            }
        }
    }

    public function has(string $resourceName, ?string $applicationName = null): bool
    {
        $applicationName = $applicationName ?? $this->defaultApplicationName;

        return \array_key_exists($resourceName, $this->resources[$applicationName][$resourceName]);
    }
}
