<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Menu;

use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Event\Events\MenuEvent;
use LAG\AdminBundle\Menu\Factory\MenuItemFactoryInterface;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;

class CreateTopMenuListener
{
    private AdminHelperInterface $adminHelper;
    private TranslationHelperInterface $translationHelper;
    private MenuItemFactoryInterface $menuItemFactory;

    public function __construct(
        AdminHelperInterface $adminHelper,
        TranslationHelperInterface $translationHelper,
        MenuItemFactoryInterface $menuItemFactory
    ) {
        $this->adminHelper = $adminHelper;
        $this->translationHelper = $translationHelper;
        $this->menuItemFactory = $menuItemFactory;
    }

    public function __invoke(MenuEvent $event): void
    {
        if ($event->getMenuName() !== 'top' || !$this->adminHelper->hasAdmin()) {
            return;
        }
        $admin = $this->adminHelper->getAdmin();
        $menu = $event->getMenu();

        // Do not add the return link when we already are on the list action
        if (!$admin->getAction()->getConfiguration()->shouldAddReturnLink()) {
            return;
        }
        $child = $this->menuItemFactory->create('return', [
            'admin' => $admin->getName(),
            'action' => 'list',
            'text' => $this->translationHelper->transWithPattern('return', [], null, null, null, 'ui'),
            'icon' => 'fas fa-arrow-left',
            'linkAttributes' => ['class' => 'btn btn-info btn-icon-split btn-sm'],
        ]);
        $menu->addChild($child);
    }
}
