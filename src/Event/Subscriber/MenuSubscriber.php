<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Exception\Exception;
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
     * @var AdminHelperInterface
     */
    private $adminHelper;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::PRE_MENU_CONFIGURATION => 'defineMenuConfiguration',
        ];
    }

    public function __construct(
        bool $menuEnabled,
        ResourceRegistryInterface $registry,
        AdminHelperInterface $adminHelper,
        array $menuConfigurations = []
    ) {
        $this->menuEnabled = $menuEnabled;
        $this->registry = $registry;
        $this->adminHelper = $adminHelper;
        $this->menuConfigurations = $menuConfigurations;
    }

    public function defineMenuConfiguration(MenuConfigurationEvent $event): void
    {
        if (!$this->supports($event)) {
            return;
        }
        $menuName = $event->getMenuName();
        $menu = $this->menuConfigurations[$menuName];

        if ($menu === null) {
            $menu = [];
        }

        if ($menuName === 'left') {
            $menu = $this->addDefaultLeftMenu($menu);
        }

        if ($menuName === 'top') {
            $menu = $this->addDefaultTopMenu($menu);
        }
        $menu = $this->configureDefaultChildren($event->getMenuName(), $menu);

        // Set defaults menu configuration to be build
        $event->setMenuConfiguration($menu);
    }

    private function supports(MenuConfigurationEvent $event): bool
    {
        if (!$this->menuEnabled) {
            return false;
        }

        if (!key_exists($event->getMenuName(), $this->menuConfigurations)) {
            return false;
        }
        $menuConfiguration = $this->menuConfigurations[$event->getMenuName()];

        if ($menuConfiguration === false) {
            return false;
        }

        return true;
    }

    private function addDefaultLeftMenu(array $menu): array
    {
        $resourceNames = $this->registry->keys();

        if (!empty($menu['children'])) {
            return $menu;
        }
        $menu['children'] = [];


        // The default main menu is composed by links to the list action of each admin resource
        foreach ($resourceNames as $resourceName) {
            $menu['children'][$resourceName] = [
                'admin' => $resourceName,
                'action' => 'list',
            ];
        }

        return $menu;
    }

    private function configureDefaultChildren(string $menuName, array $menu): array
    {
        if (empty($menu['children'])) {
            $menu['children'] = [];
        }

        foreach ($menu['children'] as $itemName => $itemConfiguration) {
            if (null === $itemConfiguration) {
                $itemConfiguration = [];
            }

            // When an url is set, nothing to add, the item menu can be build
            if (key_exists('url', $itemConfiguration)) {
                continue;
            }

            // If the key "admin' is missing, we try to find an admin resource with the same name
            if (!key_exists('admin', $itemConfiguration) && $this->registry->has($itemName)) {
                $itemConfiguration['admin'] = $itemName;
            }

            // The default admins action is list
            if (key_exists('admin', $itemConfiguration) && !key_exists('action', $itemConfiguration)) {
                $itemConfiguration['action'] = 'list';
            }

            // At this point, an pair admin/action or an url or an admin should be defined
            if (!key_exists('admin', $itemConfiguration)) {
                throw new Exception(
                    sprintf(
                        'The configuration of the children "%s" in the menu "%s" is invalid : no admin/action nor url configured, and no admin with the name "%s" exists',
                        $itemName,
                        $menuName,
                        $itemName
                    )
                );
            }
            $menu['children'][$itemName] = $itemConfiguration;
        }

        return $menu;
    }

    private function addDefaultTopMenu(array $menu): array
    {
        $admin = $this->adminHelper->getCurrent();
        $menu['attributes'] = [
            'class' => 'navbar-nav mr-auto admin-menu-top',
        ];

        // Auto return link is be optional. It is configured by default for the edit, create and delete actions
        if ($admin !== null && $admin->getAction()->getConfiguration()->get('add_return')) {
            $menu['children']['return'] = [
                'admin' => $admin->getName(),
                'action' => 'list',
                'text' => $admin->getConfiguration()->getTranslationKey('return'),
                'icon' => 'fas fa-arrow-left',
            ];
        }

        return $menu;
    }
}
