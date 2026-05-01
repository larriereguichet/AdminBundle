<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Security;

interface RolesOwnerInterface
{
    // TODO rename getRoles
    /**
     * @return array<int, string>|null
     */
    public function getPermissions(): ?array;
}
