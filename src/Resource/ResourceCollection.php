<?php

namespace LAG\AdminBundle\Resource;

use LAG\AdminBundle\Exception\Exception;

class ResourceCollection
{
    /**
     * @var AdminResource[]
     */
    protected $items = [];

    public function add(AdminResource $resource)
    {
        $this->items[$resource->getName()] = $resource;
    }

    /**
     * @return bool
     */
    public function has(string $resourceName)
    {
        return array_key_exists($resourceName, $this->items);
    }

    /**
     * @param string $resourceName
     *
     * @return AdminResource
     *
     * @throws Exception
     */
    public function get($resourceName)
    {
        if (!$this->has($resourceName)) {
            throw new Exception('Resource with name "'.$resourceName.'" not found');
        }

        return $this->items[$resourceName];
    }

    /**
     * @return AdminResource[]
     */
    public function all()
    {
        return $this->items;
    }
}
