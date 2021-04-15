<?php

namespace LAG\AdminBundle\Security\Helper;

use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Factory\Configuration\ConfigurationFactoryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityHelper
{
    private Security $security;
    private ConfigurationFactoryInterface $configurationFactory;
    private ResourceRegistryInterface $registry;

    public function __construct(
        Security $security,
        ConfigurationFactoryInterface $configurationFactory,
        ResourceRegistryInterface $registry
    ) {
        $this->security = $security;
        $this->configurationFactory = $configurationFactory;
        $this->registry = $registry;
    }

    public function isActionAllowed(string $adminName, string $actionName): bool
    {
        $resource = $this->registry->get($adminName);
        $adminConfiguration = $this->configurationFactory->createAdminConfiguration(
            $adminName,
            $resource->getConfiguration()
        );

        if (!$adminConfiguration->hasAction($actionName)) {
            return false;
        }
        $actionConfiguration = $this->configurationFactory->createActionConfiguration(
            $actionName,
            $adminConfiguration->getAction($actionName)
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
