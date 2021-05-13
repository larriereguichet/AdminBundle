<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Menu\Factory;

use Knp\Menu\ItemInterface;

interface MenuItemFactoryInterface
{
    public function create(string $name, array $options = []): ItemInterface;
}
