<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Security\Voter\OperationPermissionVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class SecurityHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private ResourceRegistryInterface $registry,
        private Security $security,
    ) {
    }

    public function isOperationAllowed(string $resourceName, string $operationName, ?string $applicationName = null): bool
    {
        $operation = $this->registry->get($resourceName, $applicationName)->getOperation($operationName);

        return $this->security->isGranted(OperationPermissionVoter::RESOURCE_ACCESS, $operation);
    }
}
