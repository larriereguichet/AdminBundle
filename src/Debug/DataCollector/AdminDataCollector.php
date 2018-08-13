<?php

namespace LAG\AdminBundle\Debug\DataCollector;

use LAG\AdminBundle\Resource\AdminResource;
use LAG\AdminBundle\Resource\ResourceCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class AdminDataCollector extends DataCollector
{
    /**
     * @var ResourceCollection
     */
    private $resourceCollection;

    /**
     * AdminDataCollector constructor.
     *
     * @param ResourceCollection $resourceCollection
     */
    public function __construct(ResourceCollection $resourceCollection)
    {
        $this->resourceCollection = $resourceCollection;
    }

    /**
     * @param Request         $request
     * @param Response        $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $data = [];

        /** @var AdminResource $resource */
        foreach ($this->resourceCollection->all() as $resource) {
            $data[$resource->getName()] = [
                'entity_class' => $resource->getEntityClass(),
                'configuration' => $resource->getConfiguration(),
            ];
        }
        $this->data = $data;
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'Admin';
    }

    public function reset()
    {
        $this->data = [];
    }
}
