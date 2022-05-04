<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Action;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\View\ActionView;

interface ActionInterface
{
    public function getName(): string;

    public function getConfiguration(): ActionConfiguration;

    public function createView(): ActionView;
}
