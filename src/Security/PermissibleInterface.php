<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Security;

interface PermissibleInterface
{
    public function getPermissions(): ?array;
}
