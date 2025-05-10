<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Security\Voter\OperationPermissionVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class SecurityHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private OperationFactoryInterface $operationFactory,
        private Security $security,
    ) {
    }

    public function isOperationAllowed(string $operationName): bool
    {
        $operation = $this->operationFactory->create($operationName);

        return $this->security->isGranted(OperationPermissionVoter::RESOURCE_ACCESS, $operation);
    }
}
