<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Factory\MenuFactory;
use LAG\AdminBundle\Resource\ResourceCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;

    /**
     * @var MenuFactory
     */
    private $menuFactory;

    /**
     * @var ResourceCollection
     */
    private $resourceCollection;

    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::MENU => 'buildLeftMenu',
        ];
    }

    /**
     * MenuSubscriber constructor.
     *
     * @param ApplicationConfigurationStorage $storage
     * @param MenuFactory                     $menuFactory
     * @param ResourceCollection              $resourceCollection
     */
    public function __construct(
        ApplicationConfigurationStorage $storage,
        MenuFactory $menuFactory,
        ResourceCollection $resourceCollection
    ) {
        $this->applicationConfiguration = $storage->getConfiguration();
        $this->menuFactory = $menuFactory;
        $this->resourceCollection = $resourceCollection;
    }

    public function buildLeftMenu()
    {
        if (!$this->applicationConfiguration->getParameter('enable_menus')) {
            return;
        }
        $menuConfiguration = [];

        foreach ($this->resourceCollection->all() as $resource) {
            $configuration = $resource->getConfiguration();

            // Add only entry for the "list" action
            if (!array_key_exists('list', $configuration['actions'])) {
                continue;
            }
            $menuConfiguration[] = [
                'text' => ucfirst($resource->getName()),
                'admin' => $resource->getName(),
                'action' => 'list',
            ];
        }

        if (!$this->menuFactory->hasMenu('left')) {
            $this->menuFactory->create('left', $menuConfiguration);
        } else {
            $menu = $this->menuFactory->getMenu('left');

            foreach ($menuConfiguration as $item) {
                $menu->addItem($this->menuFactory->createMenuItem($item));
            }
        }
    }
}
