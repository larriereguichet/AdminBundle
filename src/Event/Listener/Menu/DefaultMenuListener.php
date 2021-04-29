<?php

namespace LAG\AdminBundle\Event\Listener\Menu;

use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Event\Events\Configuration\MenuConfigurationEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;

class DefaultMenuListener
{
    private ResourceRegistryInterface $registry;
    private AdminHelperInterface $adminHelper;
    private TranslationHelperInterface $translationHelper;
    private array $menuConfigurations;

    public function __construct(
        ResourceRegistryInterface $registry,
        AdminHelperInterface $adminHelper,
        TranslationHelperInterface $translationHelper,
        array $menuConfigurations = []
    ) {
        $this->registry = $registry;
        $this->adminHelper = $adminHelper;
        $this->menuConfigurations = $menuConfigurations;
        $this->translationHelper = $translationHelper;
    }

    public function __invoke(MenuConfigurationEvent $event): void
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
        if (!\array_key_exists($event->getMenuName(), $this->menuConfigurations)) {
            return false;
        }
        $menuConfiguration = $this->menuConfigurations[$event->getMenuName()];

        if (false === $menuConfiguration) {
            return false;
        }

        return true;
    }

    private function addDefaultLeftMenu(array $menu): array
    {
        $menu['attributes']['id'] = 'accordionSidebar';
        $menu['attributes']['class'] = 'navbar-nav bg-gradient-primary sidebar sidebar-dark accordion';
        $menu['extras']['brand'] = true;
        $menu['extras']['homepage'] = true;

        if (!empty($menu['children']) && \is_array($menu['children'])) {
            foreach ($menu['children'] as $index => $item) {
                if (empty($item['attributes']['class'])) {
                    $item['attributes']['class'] = 'nav-item';
                }

                if (empty($item['linkAttributes']['class'])) {
                    $item['linkAttributes']['class'] = 'nav-link';
                }
                $menu['children'][$index] = $item;
            }

            return $menu;
        }
        $menu['children'] = [];

        // The default main menu is composed by links to the list action of each admin resource
        foreach ($this->registry->all() as $resourceName => $resource) {
            if (empty($resource->getConfiguration()['actions']) || !\array_key_exists('list', $resource->getConfiguration()['actions'])) {
                continue;
            }
            $menu['children'][$resourceName] = [
                'admin' => $resourceName,
                'action' => 'list',
                'attributes' => [
                    'class' => 'nav-item',
                ],
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
                throw new Exception(sprintf(
                    'The configuration of the children "%s" in the menu "%s" is invalid : no admin/action nor url configured, and no admin with the name "%s" exists',
                    $itemName,
                    $menuName,
                    $itemName
                ));
            }
            $menu['children'][$itemName] = $itemConfiguration;
        }

        return $menu;
    }

    private function addDefaultTopMenu(array $menu): array
    {
        if (!$this->adminHelper->hasAdmin()) {
            return $menu;
        }
        $admin = $this->adminHelper->getAdmin();

        // Auto return link is be optional. It is configured by default for the edit, create and delete actions
        if ($admin !== null) {
            $menu['children']['return'] = [
                'admin' => $admin->getName(),
                'action' => 'list',
                'text' => $this->translationHelper->transWithPattern('return', [], null, null, null,'ui'),
                'icon' => 'fas fa-arrow-left',
                'linkAttributes' => ['class' => 'btn btn-info btn-icon-split btn-sm'],
            ];
        }

        return $menu;
    }
}
