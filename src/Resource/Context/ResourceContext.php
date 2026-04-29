<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\UnsupportedRequestException;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\ResourceInterface;

final class ResourceContext implements ResourceContextInterface
{
    private ?ResourceInterface $resource = null;
    private ?OperationInterface $operation = null;

    public function getResource(): ResourceInterface
    {
        if ($this->resource === null) {
            throw new UnsupportedRequestException('The current request is not supported by any resource');
        }

        return $this->resource;
    }

    public function setResource(ResourceInterface $resource): void
    {
        $this->resource = $resource;
    }

    public function hasResource(): bool
    {
        return $this->resource !== null;
    }

    public function getOperation(): OperationInterface
    {
        if ($this->operation === null) {
            throw new UnsupportedRequestException('The current request is not supported by any resource or operation');
        }

        return $this->operation;
    }

    public function setOperation(OperationInterface $operation): void
    {
        if ($this->operation !== null) {
            throw new Exception('The request operation is already set.');
        }
        $this->operation = $operation;
    }

    public function hasOperation(): bool
    {
        return $this->operation !== null;
    }
}
