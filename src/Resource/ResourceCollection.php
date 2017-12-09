<?php

namespace LAG\AdminBundle\Resource;

use LAG\AdminBundle\Exception\Exception;

class ResourceCollection
{
    /**
     * @var Resource[]
     */
    protected $items = [];

    public function add(Resource $resource)
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
     * @return Resource
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
     * @return Resource[]
     */
    public function all()
    {
        return $this->items;
    }
}
