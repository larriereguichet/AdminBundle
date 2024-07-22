<?php

namespace LAG\AdminBundle\Resource\Metadata;

readonly class ResourceUrl
{
    public function __construct(
        private ?string $resource,
        private ?string $operation,
        private ?string $application,
    ) {
    }

    public function getResource(): ?string
    {
        return $this->resource;
    }

    public function getOperation(): ?string
    {
        return $this->operation;
    }

    public function getApplication(): ?string
    {
        return $this->application;
    }
}
