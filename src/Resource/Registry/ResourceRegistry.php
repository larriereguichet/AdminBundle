<?php

namespace LAG\AdminBundle\Resource\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\AdminResource;
use LAG\AdminBundle\Resource\Loader\ResourceLoader;

class ResourceRegistry implements ResourceRegistryInterface
{
    /**
     * @var AdminResource[]
     */
    protected $items = [];

    /**
     * @var string
     */
    private $resourcesPath;

    public function __construct(string $resourcesPath)
    {
        $this->resourcesPath = $resourcesPath;
        $this->load();
    }

    public function add(AdminResource $resource): void
    {
        $this->items[$resource->getName()] = $resource;
    }

    public function has(string $resourceName): bool
    {
        return array_key_exists($resourceName, $this->items);
    }

    public function get($resourceName): AdminResource
    {
        if (!$this->has($resourceName)) {
            throw new Exception('Resource with name "'.$resourceName.'" not found');
        }

        return $this->items[$resourceName];
    }

    public function all(): array
    {
        return $this->items;
    }

    public function keys(): array
    {
        return array_keys($this->items);
    }

    public function remove(string $resourceName): void
    {
        if (!$this->has($resourceName)) {
            throw new Exception('Resource with name "'.$resourceName.'" not found');
        }
        unset($this->items[$resourceName]);
    }

    protected function load(): void
    {
        $loader = new ResourceLoader();
        $data = $loader->load($this->resourcesPath);

        foreach ($data as $name => $admin) {
            $this->add(new AdminResource($name, $admin));
        }
    }
}
