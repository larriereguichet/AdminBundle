<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin\Resource\Registry;

use LAG\AdminBundle\Admin\Resource\AdminResource;
use LAG\AdminBundle\Admin\Resource\Loader\ResourceLoader;
use LAG\AdminBundle\Exception\Exception;

class ResourceRegistry implements ResourceRegistryInterface
{
    /**
     * @var AdminResource[]
     */
    protected array $items = [];
    private string $resourcesPath;

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
        return \array_key_exists($resourceName, $this->items);
    }

    public function get(string $resourceName): AdminResource
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
