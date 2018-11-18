<?php

namespace LAG\AdminBundle\Tests\Fixtures;

use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Resource\AdminResource;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FakeAdmin
{
    public function __construct(
        AdminResource $resource,
        AdminConfiguration $configuration,
        EventDispatcherInterface $eventDispatcher
    ) {
    }
}
