<?php

namespace LAG\AdminBundle\Menu;

class Menu
{
    /**
     * @var MenuItem[]
     */
    private $items = [];

    public function addItem(MenuItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return MenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
