<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Menu;

use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Event\Events\Configuration\MenuConfigurationEvent;
use LAG\AdminBundle\Exception\Exception;

class MenuItemConfigurationListener
{
    private ResourceRegistryInterface $registry;

    public function __construct(ResourceRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function __invoke(MenuConfigurationEvent $event): void
    {
        $menuConfiguration = $event->getMenuConfiguration();
        $menuName = $event->getMenuName();
        $menuConfiguration['children'] = $menuConfiguration['children'] ?? [];

        foreach ($menuConfiguration['children'] as $itemName => $itemConfiguration) {
            if (null === $itemConfiguration) {
                $itemConfiguration = [];
            }

            if ($menuName === 'top') {
                if (empty($itemConfiguration['attributes']['class'])) {
                    $itemConfiguration['attributes']['class'] = '';
                }
                $itemConfiguration['attributes']['class'] .= '';
                $itemConfiguration['linkAttributes'] = [];
            }
            // When an url is set, nothing to add, the item menu can be build
            if (\array_key_exists('url', $itemConfiguration)) {
                continue;
            }

            // If the key "admin' is missing, we try to find an admin resource with the same name
            if (!\array_key_exists('admin', $itemConfiguration) && $this->registry->has($itemName)) {
                $itemConfiguration['admin'] = $itemName;
            }

            // The default admins action is list
            if (\array_key_exists('admin', $itemConfiguration) && empty($itemConfiguration['action'])) {
                $itemConfiguration['action'] = 'list';
            }

            // At this point, an pair admin/action or an url or an admin should be defined
            if (!\array_key_exists('admin', $itemConfiguration)) {
                throw new Exception(sprintf('The configuration of the children "%s" in the menu "%s" is invalid : no admin/action nor url configured, and no admin with the name "%s" exists', $itemName, $menuName, $itemName));
            }
            $menuConfiguration['children'][$itemName] = $itemConfiguration;
        }
        $event->setMenuConfiguration($menuConfiguration);
    }
}
