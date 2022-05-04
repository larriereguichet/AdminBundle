<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Request;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\Events\RequestEvent;
use LAG\AdminBundle\Security\Helper\SecurityHelper;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityListener
{
    private ApplicationConfiguration $applicationConfiguration;
    private SecurityHelper $security;

    public function __construct(ApplicationConfiguration $applicationConfiguration, SecurityHelper $security)
    {
        $this->applicationConfiguration = $applicationConfiguration;
        $this->security = $security;
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$this->applicationConfiguration->isSecurityEnabled()) {
            return;
        }
        $admin = $event->getAdmin();
        $user = $this->security->getUser();
        $allowedRoles = $event->getAdmin()->getConfiguration()->getPermissions();

        if (!$this->security->isGranted($allowedRoles)) {
            throw new AccessDeniedException(sprintf('The user with roles "%s" is not granted. Allowed roles are "%s"', implode('", "', $user->getRoles()), implode('", "', $allowedRoles)));
        }

        // Do not use the $admin->getAction() as the action is not set yet
        if (!$this->security->isActionAllowed($admin->getName(), $event->getAction()->getName())) {
            throw new AccessDeniedException(sprintf('The action "%s" is not allowed for the admin "%s"', $admin->getAction()->getName(), $admin->getName()));
        }
    }
}
