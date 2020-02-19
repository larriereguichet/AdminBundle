<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private $menuEnabled;

    /**
     * @var ResourceRegistryInterface
     */
    private $registry;

    /**
     * @var array
     */
    private $menuConfigurations;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::MENU_CONFIGURATION => 'defineMenuConfiguration',
        ];
    }

    public function __construct(bool $menuEnabled, ResourceRegistryInterface $registry, array $menuConfigurations = [])
    {
        $this->menuEnabled = $menuEnabled;
        $this->registry = $registry;
        $this->menuConfigurations = $menuConfigurations;
    }

    public function defineMenuConfiguration(MenuConfigurationEvent $event)
    {
        if (!$this->menuEnabled || !key_exists($event->getMenuName(), $this->menuConfigurations)) {
            return;
        }
        $menuConfiguration = $this->menuConfigurations[$event->getMenuName()];

        if (!is_array($menuConfiguration)) {
            $menuConfiguration = [];
        }
        $menuConfiguration = array_merge_recursive($menuConfiguration, $event->getMenuConfiguration());
        $resourceNames = $this->registry->keys();

        if (!key_exists('children', $menuConfiguration) || !is_array($menuConfiguration['children'])) {
            $menuConfiguration['children'] = [];

            if ('left' === $event->getMenuName()) {
                foreach ($resourceNames as $resourceName) {
                    $menuConfiguration['children'][$resourceName] = [];
                }
            }
        }

        foreach ($menuConfiguration['children'] as $itemName => $itemConfiguration) {
            if (null === $itemConfiguration) {
                $itemConfiguration = [];
            }

            // When an url is set, nothing to add, the item menu can be build
            if (key_exists('url', $itemConfiguration)) {
                $menuConfiguration[$itemName] = $itemConfiguration;

                continue;
            }

            // If the key "admin' is missing, we try to find an admin resource with the same name
            if (!key_exists('admin', $itemConfiguration) && in_array($itemName, $resourceNames)) {
                $itemConfiguration['admin'] = $itemName;
            }

            // The default admins action is list
            if (key_exists('admin', $itemConfiguration) && !key_exists('action', $itemConfiguration)) {
                $itemConfiguration['action'] = 'list';
            }

            $menuConfiguration['children'][$itemName] = $itemConfiguration;
        }

        // Set defaults menu configuration to be build
        $event->setMenuConfiguration($menuConfiguration);
    }
}
