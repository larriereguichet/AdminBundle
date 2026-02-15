<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Menu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

final readonly class UserMenuBuilder
{
    public function __construct(
        private readonly FactoryInterface $factory,
    ) {
    }

    /** @param array<string, mixed> $options */
    public function build(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root', $options);
        $menu->addChild('lag_admin.security.logout', [
            'route' => 'lag_admin_logout',
            'extras' => ['icon' => 'sign-out-alt'],
        ]);

        return $menu;
    }
}
