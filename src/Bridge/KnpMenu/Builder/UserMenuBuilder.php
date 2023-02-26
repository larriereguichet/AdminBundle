<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UserMenuBuilder extends AbstractMenuBuilder
{
    public function __construct(
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($factory, $eventDispatcher);
    }

    public function getName(): string
    {
        return 'user';
    }

    protected function buildMenu(ItemInterface $menu): void
    {
        $menu->addChild('lag_admin.security.logout', [
            'route' => 'lag_admin.logout',
            'extras' => ['icon' => 'sign-out-alt'],
        ]);
    }
}
