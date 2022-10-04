<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\Admin;
use Symfony\Contracts\EventDispatcher\Event;

class ConfigurationEvent extends Event
{
    public function __construct(
        public readonly Admin $admin,
    ) {
    }
}
