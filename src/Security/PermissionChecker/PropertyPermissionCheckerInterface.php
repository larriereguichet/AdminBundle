<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Security\PermissionChecker;

use LAG\AdminBundle\Security\PermissibleInterface;

interface PropertyPermissionCheckerInterface
{
    public function isGranted(PermissibleInterface $subject): bool;
}
