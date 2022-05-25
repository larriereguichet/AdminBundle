<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Action;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\View\ActionView;
use LAG\AdminBundle\Admin\AdminInterface;

interface ActionInterface
{
    public function getName(): string;

    public function getAdmin(): AdminInterface;

    public function getConfiguration(): ActionConfiguration;

    public function createView(): ActionView;
}
