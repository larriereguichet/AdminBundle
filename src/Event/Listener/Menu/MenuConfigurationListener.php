<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Menu;

use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Event\Events\Configuration\MenuConfigurationEvent;

class MenuConfigurationListener
{
    private array $menusConfiguration;
    private AdminHelperInterface $helper;

    public function __construct(array $menusConfiguration, AdminHelperInterface $helper)
    {
        $this->helper = $helper;
        $this->menusConfiguration = $menusConfiguration;
    }

    public function __invoke(MenuConfigurationEvent $event): void
    {
        if (!$this->helper->hasAdmin()) {
            return;
        }
        $admin = $this->helper->getAdmin();
        $menuConfiguration = $event->getMenuConfiguration();
        $menuConfigurationFromAction = $admin->getAction()->getConfiguration()->getMenus();
        $inherits = $menuConfiguration['inherits'] ?? false;
        $menuConfiguration = $menuConfigurationFromAction[$event->getMenuName()] ?? [];

        if ($inherits) {
            $menuConfiguration = array_merge($this->menusConfiguration[$event->getMenuName()] ?? [], $menuConfiguration);
            $menuConfiguration = array_merge($menuConfiguration, $menuConfigurationFromAction[$event->getMenuName()]);
        }
        $event->setMenuConfiguration($menuConfiguration);
    }
}
