<?php

namespace LAG\AdminBundle\Action\View;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;

class ActionView
{
    public function __construct(
        private string $name,
        private ActionConfiguration $configuration,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConfiguration(): ActionConfiguration
    {
        return $this->configuration;
    }

    public function getTitle(): string
    {
        return $this->configuration->getTitle();
    }
}
