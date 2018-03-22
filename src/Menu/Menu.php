<?php

namespace LAG\AdminBundle\Menu;

class Menu
{
    /**
     * @var MenuItem[]
     */
    private $items = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $containerCssClasses = [];

    /**
     * @var array
     */
    private $itemCssClasses = [];

    /**
     * Menu constructor.
     *
     * @param string $name
     * @param array  $containerCssClasses
     * @param array  $itemCssClasses
     */
    public function __construct(string $name, array $containerCssClasses = [], array $itemCssClasses = [])
    {
        $this->name = $name;
        $this->containerCssClasses = $containerCssClasses;
        $this->itemCssClasses = $itemCssClasses;
    }

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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getContainerCssClasses(): array
    {
        return $this->containerCssClasses;
    }

    /**
     * @return array
     */
    public function getItemCssClasses(): array
    {
        return $this->itemCssClasses;
    }
}
