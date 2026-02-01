<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Security\PermissionChecker;

use LAG\AdminBundle\Security\RolesOwnerInterface;

interface PropertyPermissionCheckerInterface
{
    public function isGranted(RolesOwnerInterface $subject): bool;
}
