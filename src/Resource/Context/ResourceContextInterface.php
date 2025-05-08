<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Metadata\Resource;

interface ResourceContextInterface
{
    /**
     * Return the current resource. If no resource is found, an exception will be thrown.
     *
     * @return Resource The current resource
     */
    public function getResource(): Resource;

    /**
     * Return true if the current request has a resource.
     */
    public function hasResource(): bool;
}
