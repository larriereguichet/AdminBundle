<?php

namespace LAG\AdminBundle\Event\Listener\Menu;

use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Event\Events\Configuration\MenuConfigurationEvent;

class MenuConfigurationListener
{
    private AdminHelperInterface $helper;

    public function __construct(AdminHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function __invoke(MenuConfigurationEvent $event): void
    {
        if (!$this->helper->hasAdmin()) {
            return;
        }
        $admin = $this->helper->getAdmin();
        $menuConfiguration = $event->getMenuConfiguration();
        $menuName = $event->getMenuName();
        $menuConfigurationFromAction = $admin->getAction()->getConfiguration()->getMenus();
        $inherits = empty($menuConfiguration['inherits']) || false === $menuConfiguration['inherits'];

        if (empty($menuConfigurationFromAction[$menuName])) {
            return;
        }

        if ($inherits) {
            $menuConfiguration = array_merge_recursive($menuConfiguration, $menuConfigurationFromAction[$menuName]);
        } else {
            $menuConfiguration = $menuConfigurationFromAction[$menuName];
        }
        $event->setMenuConfiguration($menuConfiguration);
    }
}
