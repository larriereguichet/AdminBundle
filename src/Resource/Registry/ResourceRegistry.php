<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\UnexpectedTypeException;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Metadata\Locator\MetadataLocatorInterface;

class ResourceRegistry implements ResourceRegistryInterface
{
    /** @var AdminResource[] */
    private array $definitions = [];
    private bool $loaded = false;

    public function __construct(
        /** @var array<int, string> $resourcePaths */
        private array $resourcePaths,
        private MetadataLocatorInterface $locator,
        private ResourceFactoryInterface $resourceFactory,
    ) {
    }

    public function load(): void
    {
        if ($this->loaded) {
            return;
        }

        foreach ($this->resourcePaths as $path) {
            $resources = $this->locator->locateCollection($path);

            foreach ($resources as $resource) {
                if (!$resource instanceof AdminResource) {
                    throw new UnexpectedTypeException($resource, AdminResource::class);
                }

                if (!$resource->getName()) {
                    throw new Exception('The admin resource has no name');
                }
                $this->definitions[$resource->getName()] = $resource;
            }
        }
        $this->loaded = true;
    }

    public function has(string $resourceName): bool
    {
        return \array_key_exists($resourceName, $this->definitions);
    }

    public function get(string $resourceName): AdminResource
    {
        $this->load();

        if (!$this->has($resourceName)) {
            throw new Exception('Resource with name "'.$resourceName.'" not found');
        }

        return $this->resourceFactory->create($this->definitions[$resourceName]);
    }

    public function all(): iterable
    {
        $this->load();

        foreach ($this->definitions as $definition) {
            yield $this->resourceFactory->create($definition);
        }
    }
}
