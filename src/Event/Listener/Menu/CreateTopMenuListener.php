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
    public function __construct(
        private AdminHelperInterface $adminHelper,
        private TranslationHelperInterface $translationHelper,
        private MenuItemFactoryInterface $menuItemFactory,
        private ApplicationConfiguration $appConfig
    ) {
    }

    public function __invoke(MenuEvent $event): void
    {
        if (!$this->adminHelper->hasAdmin()) {
            return;
        }
        $admin = $this->adminHelper->getAdmin();
        $menu = $event->getMenu();

        // Do not add the return link when we are on the list action
        if (!$admin->getAction()->getConfiguration()->shouldAddReturnLink() || !$admin->getConfiguration()->hasAction('list')) {
            return;
        }
        $child = $this->menuItemFactory->create('return', [
            'admin' => $admin->getName(),
            'action' => 'list',
            'text' => $this->translationHelper->transWithPattern('return', [], 'ui'),
            'icon' => 'fas fa-arrow-left',
            'route' => $this->appConfig->getRouteName($admin->getName(), 'list'),
        ]);
        $menu->addChild($child);
    }
}
