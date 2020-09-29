<?php

namespace LAG\AdminBundle\Debug\DataCollector;

use LAG\AdminBundle\Bridge\KnpMenu\Provider\MenuProvider;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Exception\ConfigurationException;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

class AdminDataCollector extends DataCollector
{
    /**
     * @var ResourceRegistryInterface
     */
    private $registry;

    /**
     * @var ApplicationConfigurationStorage
     */
    private $storage;

    /**
     * @var MenuProvider
     */
    private $menuProvider;

    /**
     * AdminDataCollector constructor.
     */
    public function __construct(
        ResourceRegistryInterface $registry,
        ApplicationConfigurationStorage $storage,
        MenuProvider $menuProvider
    ) {
        $this->registry = $registry;
        $this->storage = $storage;
        $this->menuProvider = $menuProvider;
    }

    public function collect(Request $request, Response $response, Throwable $exception = null)
    {
        $data = [
            'admins' => [],
            'application' => [],
            'menus' => [],
        ];

        if ($exception instanceof ConfigurationException) {
            $data['error'] = 'An error has occurred during the configuration resolving. Data can not displayed';
            $data['configuration'] = $exception->getConfiguration();
        } else {
            foreach ($this->registry->all() as $resource) {
                $data['admins'][$resource->getName()] = [
                    'entity_class' => $resource->getEntityClass(),
                    'configuration' => $resource->getConfiguration(),
                ];
            }

            // When the application configuration is not defined or resolved, we can not access to the admin/menus
            // configuration
            if ($this->storage->isFrozen()) {
                $data['application'] = $this->storage->getConfiguration()->all();

                foreach ($this->menuProvider->all() as $menuName => $menu) {
                    $data['menus'][$menuName] = [
                        'attributes' => $menu->getAttributes(),
                        'displayed' => $menu->isDisplayed(),
                    ];

                    foreach ($menu->getChildren() as $childName => $child) {
                        $data['menus'][$menuName]['children'][$childName] = [
                            'attributes' => $child->getAttributes(),
                            'displayed' => $child->isDisplayed(),
                            'uri' => $child->getUri(),
                        ];
                    }
                }
            }
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
