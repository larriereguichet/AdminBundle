<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Data;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\Security\PasswordAuthenticatedResourceInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class PasswordListener
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(DataEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof PasswordAuthenticatedResourceInterface || !$data->getPlainPassword()) {
            return;
        }
        $encodedPassword = $this->passwordHasher->hashPassword($data, $data->getPlainPassword());
        $data->setPassword($encodedPassword);
    }
}
