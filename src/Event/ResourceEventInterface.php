<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\ResourceInterface;

interface ResourceEventInterface
{
    public function getResource(): ResourceInterface;
}
