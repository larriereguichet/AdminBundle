<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Entity;

use LAG\AdminBundle\Security\PasswordAuthenticatedResourceInterface;

final class User implements PasswordAuthenticatedResourceInterface
{
    private ?string $password = null;
    private ?string $plainPassword = null;

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
