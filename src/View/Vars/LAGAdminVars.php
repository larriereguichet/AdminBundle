<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Vars;

use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Metadata\Application;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Registry\ApplicationRegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class LAGAdminVars implements LAGAdminVarsInterface
{
    private ?LAGAdmin $vars = null;

    public function __construct(
        private readonly ResourceContextInterface $context,
        private readonly RequestStack $requestStack,
        private readonly ApplicationRegistryInterface $applicationRegistry,
    ) {
    }

    public function getApplication(): ?Application
    {
        return $this->getVars()?->application;
    }

    public function getResource(): Resource
    {
        return $this->getVars()?->resource;
    }

    public function getOperation(): OperationInterface
    {
        return $this->getVars()?->operation;
    }

    private function getVars(): ?LAGAdmin
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null || !$this->context->supports($request)) {
            return null;
        }

        if ($this->vars === null) {
            $operation = $this->context->getOperation($request);
            $application = $this->applicationRegistry->get($operation->getResource()->getApplication());
            $this->vars = new LAGAdmin(
                application: $application,
                resource: $operation->getResource(),
                operation: $operation,
            );
        }

        return $this->vars;
    }
}
