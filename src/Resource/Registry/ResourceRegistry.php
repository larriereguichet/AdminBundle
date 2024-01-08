<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;

class ResourceRegistry implements ResourceRegistryInterface
{
    private array $resources = [];

    public function __construct(
        /** @var iterable<Resource> $resources */
        iterable $resources,
        private readonly string $defaultApplication,
        private readonly ResourceFactoryInterface $factory,
    ) {
        foreach ($resources as $resource) {
            $this->resources[$resource->getApplicationName() ?? $this->defaultApplication][$resource->getName()] = $resource;
        }
    }

    public function get(string $resourceName, ?string $applicationName = null): Resource
    {
        $applicationName = $applicationName ?? $this->defaultApplication;

        if (!$this->has($resourceName, $applicationName)) {
            throw new Exception(sprintf('Resource with name "%s" not found in the application "%s"', $resourceName, $applicationName));
        }
        $definition = $this->resources[$applicationName][$resourceName];

        return $this->factory->create($definition);
    }

    public function all(): iterable
    {
        foreach ($this->resources as $resources) {
            foreach ($resources as $resource) {
                yield $this->factory->create($resource);
            }
        }
    }

    public function has(string $resourceName, ?string $applicationName = null): bool
    {
        $applicationName = $applicationName ?? $this->defaultApplication;

        return \array_key_exists($resourceName, $this->resources[$applicationName]);
    }
}
