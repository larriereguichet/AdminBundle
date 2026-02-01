<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\Attribute\Resource;

interface ResourceEventInterface
{
    public function getResource(): Resource;
}
