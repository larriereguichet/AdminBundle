<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\ResourceInterface;

interface ResourceContextInterface
{
    /**
     * Return the current resource. If no resource is found, an exception will be thrown.
     *
     * @return ResourceInterface The current resource
     */
    public function getResource(): ResourceInterface;

    /**
     * Return true if the current request has a resource.
     */
    public function hasResource(): bool;

    /**
     * Return the current operation according to the current request parameters.
     *
     * @return OperationInterface The current operation
     */
    public function getOperation(): OperationInterface;

    /**
     * Return true if there is a supported operation in the current operation.
     */
    public function hasOperation(): bool;
}
