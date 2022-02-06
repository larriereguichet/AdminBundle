<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\ItemInterface;

interface MenuBuilderInterface
{
    public function createMenu(array $options = []): ItemInterface;
}
