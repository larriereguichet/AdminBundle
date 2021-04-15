<?php

namespace LAG\AdminBundle\Debug\DataCollector;

use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

class AdminDataCollector extends DataCollector
{
    private ResourceRegistryInterface $registry;
    private array $menuConfigurations;
    private ApplicationConfiguration $appConfig;

    public function __construct(
        ResourceRegistryInterface $registry,
        ApplicationConfiguration $appConfig,
        array $menuConfigurations = []
    ) {
        $this->registry = $registry;
        $this->menuConfigurations = $menuConfigurations;
        $this->appConfig = $appConfig;
    }

    public function collect(Request $request, Response $response, Throwable $exception = null)
    {
        $data = [
            'admins' => [],
            'application' => [],
            'menus' => [],
        ];

        foreach ($this->registry->all() as $resource) {
            $data['admins'][$resource->getName()] = [
                'entity_class' => $resource->getEntityClass(),
                'configuration' => $resource->getConfiguration(),
            ];
        }

        // When the application configuration is not defined or resolved, we can not access to the admin/menus
        // configuration
        if ($this->appConfig->isFrozen()) {
            $data['application'] = $this->appConfig->toArray();
            $data['menus'] = $this->menuConfigurations;
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
