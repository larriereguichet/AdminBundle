<?php

namespace LAG\AdminBundle\Debug\DataCollector;

use Exception;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Factory\MenuFactory;
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
     * @var MenuFactory
     */
    private $menuFactory;

    /**
     * AdminDataCollector constructor.
     *
     * @param ResourceCollection              $resourceCollection
     * @param ApplicationConfigurationStorage $applicationConfigurationStorage
     * @param MenuFactory                     $menuFactory
     */
    public function __construct(
        ResourceCollection $resourceCollection,
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        MenuFactory $menuFactory
    ) {
        $this->resourceCollection = $resourceCollection;
        $this->applicationConfigurationStorage = $applicationConfigurationStorage;
        $this->menuFactory = $menuFactory;
    }

    /**
     * @param Request         $request
     * @param Response        $response
     * @param Exception|null $exception
     */
    public function collect(Request $request, Response $response, Exception $exception = null)
    {
        $data = [
            'admins' => [],
            'application' => [],
            'menus' => $this->menuFactory->getMenus(),
        ];

        foreach ($this->resourceCollection->all() as $resource) {
            $data['admins'][$resource->getName()] = [
                'entity_class' => $resource->getEntityClass(),
                'configuration' => $resource->getConfiguration(),
            ];
        }

        foreach ($this->applicationConfigurationStorage->getConfiguration()->getParameters() as $name => $parameter) {
            if (is_array($parameter)) {
                $parameter = print_r($parameter, true);
            }

            if (is_bool($parameter)) {
                $parameter = $parameter ? 'true' : 'false';
            }
            $data['application'][$name] = $parameter;
        }
        $data['application']['admin'] = $request->attributes->get('_admin');
        $data['application']['action'] = $request->attributes->get('_action');

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
