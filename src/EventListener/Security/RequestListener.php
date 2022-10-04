<?php

namespace LAG\AdminBundle\EventListener\Security;

use LAG\AdminBundle\Metadata\Admin;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

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
            if (!$argument instanceof Admin) {
                continue;
            }
            $permissions = $argument->getCurrentOperation()->getPermissions();

            foreach ($permissions as $permission) {
                if (!$this->security->isGranted($permission, $user)) {
                    throw new AccessDeniedException();
                }
            }
        }
    }
}
