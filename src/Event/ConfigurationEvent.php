<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\AdminResource;
use Symfony\Contracts\EventDispatcher\Event;

class ConfigurationEvent extends Event
{
    public function __construct(
        public readonly AdminResource $admin,
    ) {
    }
}
