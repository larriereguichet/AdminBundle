<?php

namespace LAG\AdminBundle\Menu;

use LAG\AdminBundle\Configuration\MenuConfiguration;

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
     * @var MenuConfiguration
     */
    private $configuration;

    /**
     * Menu constructor.
     */
    public function __construct(string $name, MenuConfiguration $configuration)
    {
        $this->name = $name;
        $this->configuration = $configuration;
    }

    public function addItem(MenuItem $item)
    {
        $this->items[] = $item;
    }

    public function removeItem(int $position)
    {
        unset($this->items[$position]);
    }

    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * @return MenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function get(string $parameter)
    {
        return $this->configuration->getParameter($parameter);
    }

    public function getConfiguration(): MenuConfiguration
    {
        return $this->configuration;
    }
}
