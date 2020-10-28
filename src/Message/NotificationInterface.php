<?php

namespace LAG\AdminBundle\Message;

use Symfony\Component\Security\Core\User\UserInterface;

interface NotificationInterface
{
    public function getText(): string;

    public function getIcon(): ?string;

    public function getCreatedAt(): string;

    public function getUser(): UserInterface;

    public function getUrl(): ?string;
}
