<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events\Configuration;

use Symfony\Contracts\EventDispatcher\Event;

class MenuConfigurationEvent extends Event
{
    private string $menuName;
    private array $menuConfiguration;

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
