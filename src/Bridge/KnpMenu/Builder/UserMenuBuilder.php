<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UserMenuBuilder
{
    use MenuBuilderTrait;

    public function __construct(
        private FactoryInterface $factory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild('lag_admin.security.logout', [
            'route' => 'lag_admin.logout',
            'extras' => ['icon' => 'sign-out-alt'],
        ]);
        $this->dispatchMenuEvents('user', $menu);

        return $menu;
    }
}
