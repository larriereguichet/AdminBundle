<?php

namespace LAG\AdminBundle\Debug\DataCollector;

use Exception;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Menu\Provider\MenuProvider;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

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

    public function collect(Request $request, Response $response, Exception $exception = null)
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

        foreach ($this->storage->getConfiguration()->all() as $name => $parameter) {
            $data['application'][$name] = $parameter;
        }

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
