<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Action;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\View\ActionView;

class Action
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

    public function createView(): ActionView
    {
        return new ActionView(
            $this->name,
            $this->configuration,
        );
    }
}
