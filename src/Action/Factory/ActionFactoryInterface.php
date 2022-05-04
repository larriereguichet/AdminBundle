<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Action\Factory;

use LAG\AdminBundle\Action\ActionInterface;

interface ActionFactoryInterface
{
    /**
     * Create a new Action and configure it with the given options.
     */
    public function create(string $actionName, array $options = []): ActionInterface;
}
