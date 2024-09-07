<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Security\PermissionChecker;

use LAG\AdminBundle\Security\PermissibleInterface;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class PropertyPermissionChecker implements PropertyPermissionCheckerInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function isGranted(PermissibleInterface $subject): bool
    {
        if ($subject->getPermissions() === null) {
            return true;
        }
        $user = $this->security->getUser();

        foreach ($subject->getPermissions() as $permission) {
            if ($this->security->isGranted($permission, $user)) {
                return true;
            }
        }

        return false;
    }
}
