<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Configuration\ActionConfiguration;

class Action implements ActionInterface
{
    public function __construct(private string $name, private ActionConfiguration $configuration)
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
}
