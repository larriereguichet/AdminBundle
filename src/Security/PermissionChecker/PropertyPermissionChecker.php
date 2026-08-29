<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Security\PermissionChecker;

use LAG\AdminBundle\Security\RolesOwnerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class PropertyPermissionChecker implements PropertyPermissionCheckerInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function isGranted(RolesOwnerInterface $subject): bool
    {
        if ($subject->getRoles() === null) {
            return true;
        }
        $user = $this->security->getUser();

        return array_any($subject->getRoles(), fn ($permission) => $this->security->isGranted($permission, $user));
    }
}
