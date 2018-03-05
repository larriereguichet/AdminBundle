<?php

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Configuration\ActionConfiguration;

interface ActionInterface
{
    public function getName(): string;

    public function getConfiguration(): ActionConfiguration;
}
