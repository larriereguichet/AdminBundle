<?php

namespace LAG\AdminBundle\Debug\DataCollector;

use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
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
     * @var ApplicationConfigurationStorage
     */
    private $applicationConfigurationStorage;

    /**
     * AdminDataCollector constructor.
     *
     * @param ResourceCollection $resourceCollection
     */
    public function __construct(
        ResourceCollection $resourceCollection,
        ApplicationConfigurationStorage $applicationConfigurationStorage
    ) {
        $this->resourceCollection = $resourceCollection;
        $this->applicationConfigurationStorage = $applicationConfigurationStorage;
    }

    /**
     * @param Request         $request
     * @param Response        $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $data = [
            'admins' => [],
            'application' => $this->applicationConfigurationStorage->getConfiguration()->getParameters(),
        ];

        /** @var AdminResource $resource */
        foreach ($this->resourceCollection->all() as $resource) {
            $data['admins'][$resource->getName()] = [
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
        return 'admin.data_collector';
    }

    public function reset()
    {
        $this->data = [];
    }

    public function getData()
    {
        return $this->data;
    }
}
