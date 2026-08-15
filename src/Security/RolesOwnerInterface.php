<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Security;

interface RolesOwnerInterface
{
    /** @return array<int, string>|null */
    public function getRoles(): ?array;
}
