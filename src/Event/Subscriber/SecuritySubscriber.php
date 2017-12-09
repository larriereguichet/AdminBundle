<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Event\AdminEvents;
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
     *
     * @param ApplicationConfigurationStorage $applicationConfigurationStorage
     * @param TokenStorageInterface           $tokenStorage
     * @param AuthorizationCheckerInterface   $authorizationChecker
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
            AdminEvents::HANDLE_REQUEST => 'handleRequest',
        ];
    }

    /**
     * @param AdminEvent $event
     *
     * @throws AccessDeniedException
     */
    public function handleRequest(AdminEvent $event)
    {
        if (!$this->applicationConfiguration->getParameter('enable_security')) {
            return;
        }
        $user = $this->getUser();

        if (!$this->authorizationChecker->isGranted($user->getRoles(), $user)) {
            throw new AccessDeniedException();
        }
        $configuration = $event->getAdmin()->getConfiguration();
        $allowed = false;

        foreach ($user->getRoles() as $role) {
            if ($configuration->getParameter('permissions') === $role->getRole()) {
                $allowed = true;
            }
        }
        if (!$allowed) {
            throw new AccessDeniedException();
        }
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
