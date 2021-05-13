<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Menu;

use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\Events\MenuEvent;
use LAG\AdminBundle\Menu\Factory\MenuItemFactoryInterface;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;

class CreateTopMenuListener
{
    private AdminHelperInterface $adminHelper;
    private TranslationHelperInterface $translationHelper;
    private MenuItemFactoryInterface $menuItemFactory;
    private ApplicationConfiguration $appConfig;

    public function __construct(
        AdminHelperInterface $adminHelper,
        TranslationHelperInterface $translationHelper,
        MenuItemFactoryInterface $menuItemFactory,
        ApplicationConfiguration $appConfig
    ) {
        $this->adminHelper = $adminHelper;
        $this->translationHelper = $translationHelper;
        $this->menuItemFactory = $menuItemFactory;
        $this->appConfig = $appConfig;
    }

    public function __invoke(MenuEvent $event): void
    {
        if ($event->getMenuName() !== 'top' || !$this->adminHelper->hasAdmin()) {
            return;
        }
        $admin = $this->adminHelper->getAdmin();
        $menu = $event->getMenu();

        // Do not add the return link when we already are on the list action
        if (!$admin->getAction()->getConfiguration()->shouldAddReturnLink() || !$admin->getConfiguration()->hasAction('list')) {
            return;
        }
        $child = $this->menuItemFactory->create('return', [
            'admin' => $admin->getName(),
            'action' => 'list',
            'text' => $this->translationHelper->transWithPattern('return', [], null, null, null, 'ui'),
            'icon' => 'fas fa-arrow-left',
            'route' => $this->appConfig->getRouteName($admin->getName(), 'list'),
        ]);
        $menu->addChild($child);
    }
}
