<?php

namespace LAG\AdminBundle\Event\Events\Configuration;

use Symfony\Contracts\EventDispatcher\Event;

class AdminConfigurationEvent extends Event
{
    private string $adminName;
    private array $configuration;

    public function __construct(string $adminName, array $configuration)
    {
        $this->adminName = $adminName;
        $this->configuration = $configuration;
    }

    public function getAdminName(): string
    {
        return $this->adminName;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): AdminConfigurationEvent
    {
        $this->configuration = $configuration;

        return $this;
    }
}
