<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Metadata\OperationInterface;

interface OperationContextInterface
{
    /**
     * Return the current operation according to the current request parameters.
     *
     * @return OperationInterface The current operation
     */
    public function getOperation(): OperationInterface;

    /**
     * Return true if there is a supported operation in the current operation.
     *
     * @return bool
     */
    public function hasOperation(): bool;
}
