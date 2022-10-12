<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Security\Helper;

use LAG\AdminBundle\Action\Factory\ActionConfigurationFactoryInterface;
use LAG\AdminBundle\Admin\Factory\AdminConfigurationFactoryInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityHelper
{
    public function __construct(
        private Security $security,
        private ResourceRegistryInterface $registry
    ) {
    }

    public function isOperationAllowed(string $resourceName, string $operationName): bool
    {
        $user = $this->getUser();
        $resource = $this->registry->get($resourceName);

        if (!$resource->hasOperation($operationName)) {
            return false;
        }
        $operation = $resource->getOperation($operationName);

        foreach ($operation->getPermissions() ?? [] as $permission) {
            if (!$this->security->isGranted($permission, $user)) {
                return false;
            }
        }

        return true;
    }

    public function getUser(): UserInterface
    {
        $user = $this->security->getUser();

        if ($user === null) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}
