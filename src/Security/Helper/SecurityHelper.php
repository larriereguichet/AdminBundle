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
        private AdminConfigurationFactoryInterface $adminConfigurationFactory,
        private ActionConfigurationFactoryInterface $actionConfigurationFactory,
        private ResourceRegistryInterface $registry
    ) {
    }

    public function isActionAllowed(string $adminName, string $actionName): bool
    {
        $resource = $this->registry->get($adminName);
        $adminConfiguration = $this->adminConfigurationFactory->create(
            $adminName,
            $resource->getConfiguration()
        );

        if (!$adminConfiguration->hasAction($actionName)) {
            return false;
        }
        $actionConfiguration = $this->actionConfigurationFactory->create(
            $adminName,
            $actionName,
            $adminConfiguration->getAction($actionName),
        );

        return $this->isGranted($actionConfiguration->getPermissions());
    }

    public function isGranted(array $permissions): bool
    {
        $user = $this->getUser();

        foreach ($permissions as $permission) {
            if ($this->security->isGranted($permission, $user)) {
                return true;
            }
        }

        return false;
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
