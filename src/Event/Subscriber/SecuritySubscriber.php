<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\AdminEvent;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class SecuritySubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;

    /**
     * SecuritySubscriber constructor.
     */
    public function __construct(
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::ADMIN_HANDLE_REQUEST => 'handleRequest',
        ];
    }

    /**
     * @throws AccessDeniedException
     */
    public function handleRequest(AdminEvent $event): void
    {
        if (!$this->applicationConfiguration->getParameter('enable_security')) {
            return;
        }
        $user = $this->getUser();
        $expectedRoles = $event->getAdmin()->getConfiguration()->getPermissions();

        foreach ($expectedRoles as $role) {
            if (!$this->authorizationChecker->isGranted($role, $user)) {
                throw new AccessDeniedException(sprintf(
                    'The user with roles "%s" is not granted. Allowed roles are "%s"',
                    implode('", "', $user->getRoles()),
                    implode('", "', $expectedRoles)
                ));
            }
        }
//        $configuration =;
//        $allowed = false;
//
//        foreach ($user->getRoles() as $role) {
//            if ($configuration->getParameter('permissions') === $role) {
//                $allowed = true;
//            }
//        }
//        if (!$allowed) {
//            throw new AccessDeniedException();
//        }
    }

    /**
     * @return UserInterface
     *
     * @throws Exception
     */
    private function getUser()
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            throw new Exception('The security token is not defined');
        }
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            throw new Exception('The security user is not defined');
        }

        return $user;
    }
}
