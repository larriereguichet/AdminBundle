<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Admin\Configuration\ActionConfiguration;

interface ActionInterface
{
    public function getName(): string;

    public function getConfiguration(): ActionConfiguration;
}
