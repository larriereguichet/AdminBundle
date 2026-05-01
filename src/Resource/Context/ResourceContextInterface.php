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
     * Define the current resource. An exception is thrown if it has already been defined.
     */
    public function setResource(ResourceInterface $resource): void;

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
     * Define the current operation. An exception is thrown if it has already been defined.
     */
    public function setOperation(OperationInterface $operation): void;

    /**
     * Return true if there is a supported operation in the current operation.
     */
    public function hasOperation(): bool;
}
