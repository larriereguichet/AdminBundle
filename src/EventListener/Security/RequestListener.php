<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Security;

use LAG\AdminBundle\Metadata\AdminResource;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RequestListener
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function __invoke(ControllerArgumentsEvent $event): void
    {
        $user = $this->security->getUser();

        foreach ($event->getArguments() as $argument) {
            if (!$argument instanceof AdminResource) {
                continue;
            }
            $permissions = $argument->getCurrentOperation()->getPermissions();

            foreach ($permissions as $permission) {
                if ($user === null || !$this->security->isGranted($permission, $user)) {
                    throw new AccessDeniedException();
                }
            }
        }
    }
}
