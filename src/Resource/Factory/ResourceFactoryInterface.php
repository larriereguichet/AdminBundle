<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\Resource;

interface ResourceFactoryInterface
{
    /**
     * Create a new Admin resource instance. The resource will be validated then returned. An event could be dispatched
     * before and after the resource creation.
     */
    public function create(string $resourceName): Resource;
}
