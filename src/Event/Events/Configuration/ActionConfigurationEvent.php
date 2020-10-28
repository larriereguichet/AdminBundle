<?php

namespace LAG\AdminBundle\Event\Events\Configuration;

use Symfony\Contracts\EventDispatcher\Event;

class ActionConfigurationEvent extends Event
{
    private string $actionName;
    private array $configuration;

    public function __construct(string $actionName, array $configuration)
    {
        $this->actionName = $actionName;
        $this->configuration = $configuration;
    }

    public function getActionName(): string
    {
        return $this->actionName;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}
