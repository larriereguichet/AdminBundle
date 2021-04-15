<?php

namespace LAG\AdminBundle\Event\Listener\Request;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\Events\RequestEvent;
use LAG\AdminBundle\Security\Helper\SecurityHelper;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityListener
{
    private ApplicationConfiguration $appConfig;
    private SecurityHelper $security;

    public function __construct(ApplicationConfiguration $appConfig, SecurityHelper $security)
    {
        $this->appConfig = $appConfig;
        $this->security = $security;
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$this->appConfig->isSecurityEnabled()) {
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
