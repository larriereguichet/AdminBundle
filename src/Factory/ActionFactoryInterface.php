<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\ActionInterface;

interface ActionFactoryInterface
{
    /**
     * Create a new Action and configure it with the given options.
     */
    public function create(string $actionName, array $options = []): ActionInterface;
}
