<?php

namespace LAG\AdminBundle\Resource;

use LAG\AdminBundle\Exception\Exception;

class ResourceCollection
{
    /**
     * @var \LAG\AdminBundle\Resource\Resource[]
     */
    protected $items = [];

    public function add(\LAG\AdminBundle\Resource\Resource $resource)
    {
        $this->items[$resource->getName()] = $resource;
    }

    public function has($resourceName)
    {
        return array_key_exists($resourceName, $this->items);
    }

    /**
     * @param string $resourceName
     *
     * @return \LAG\AdminBundle\Resource\Resource
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
     * @return \LAG\AdminBundle\Resource\Resource[]
     */
    public function all()
    {
        return $this->items;
    }
}
