<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Globals;

use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Resource\Context\ApplicationContextInterface;
use LAG\AdminBundle\Resource\Context\OperationContextInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;

final readonly class LAGAdminGlobal
{
    public function __construct(
        private ApplicationContextInterface $applicationContext,
        private ResourceContextInterface $resourceContext,
        private OperationContextInterface $operationContext,
    ) {
    }

    public function getApplication(): ?Application
    {
        if (!$this->applicationContext->hasApplication()) {
            return null;
        }

        return $this->applicationContext->getApplication();
    }

    public function getResource(): ?Resource
    {
        if (!$this->resourceContext->hasResource()) {
            return null;
        }

        return $this->resourceContext->getResource();
    }

    public function getOperation(): ?OperationInterface
    {
        if (!$this->operationContext->hasOperation()) {
            return null;
        }

        return $this->operationContext->getOperation();
    }
}
