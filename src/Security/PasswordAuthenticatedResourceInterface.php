<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Security;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface PasswordAuthenticatedResourceInterface extends PasswordAuthenticatedUserInterface
{
    public function setPassword(?string $password): void;

    public function getPlainPassword(): ?string;

    public function setPlainPassword(?string $plainPassword): void;
}
