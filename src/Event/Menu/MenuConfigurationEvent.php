<?php

namespace LAG\AdminBundle\Event\Menu;

use Symfony\Contracts\EventDispatcher\Event;

class MenuConfigurationEvent extends Event
{
    /**
     * @var string
     */
    private $menuName;

    /**
     * @var array
     */
    private $menuConfiguration;

    public function __construct(string $menuName, array $menuConfiguration = [])
    {
        $this->menuName = $menuName;
        $this->menuConfiguration = $menuConfiguration;
    }

    public function getMenuName(): string
    {
        return $this->menuName;
    }

    public function setMenuConfiguration(array $menuConfiguration): void
    {
        $this->menuConfiguration = $menuConfiguration;
    }

    public function getMenuConfiguration(): array
    {
        return $this->menuConfiguration;
    }
}
