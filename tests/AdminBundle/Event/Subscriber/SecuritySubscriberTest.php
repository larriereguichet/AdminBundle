<?php

namespace LAG\AdminBundle\Tests\Event\Subscriber;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\AdminEvent;
use LAG\AdminBundle\Event\Subscriber\SecuritySubscriber;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SecuritySubscriberTest extends AdminTestBase
{
    public function testSubscribedEvents()
    {
        $this->assertArrayHasKey(Events::HANDLE_REQUEST, SecuritySubscriber::getSubscribedEvents());
    }

    public function testHandleRequest()
    {
        list($subscriber,, $tokenStorage, $authorizationChecker) = $this->createSubscriber([]);

        $user = $this->createMock(UserInterface::class);
        $user
            ->expects($this->atLeastOnce())
            ->method('getRoles')
            ->willReturn([
                'ROLE_GRANTED',
            ])
        ;

        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($user)
        ;

        $tokenStorage
            ->expects($this->atLeastOnce())
            ->method('getToken')
            ->willReturn($token)
        ;

        $authorizationChecker
            ->expects($this->atLeastOnce())
            ->method('isGranted')
            ->with([
                'ROLE_GRANTED',
            ], $user)
            ->willReturn(true)
        ;

        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->with('permissions')
            ->willReturn('ROLE_GRANTED')
        ;

        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;

        $request = new Request();

        $event = new AdminEvent($admin, $request);
        $subscriber->handleRequest($event);
    }

    public function testHandleRequestWithSecurityDisabled()
    {
        list($subscriber,, $tokenStorage) = $this->createSubscriber([
            'enable_security' => false,
        ]);

        // The token should never be retrieved when the security is disabled
        $tokenStorage
            ->expects($this->never())
            ->method('getToken')
        ;

        $admin = $this->createMock(AdminInterface::class);
        $request = new Request();

        $event = new AdminEvent($admin, $request);
        $subscriber->handleRequest($event);
    }

    /**
     * @param array $configuration
     *
     * @return SecuritySubscriber[]|MockObject[]
     */
    private function createSubscriber(array $configuration)
    {
        $applicationConfiguration = $this->createApplicationConfiguration($configuration);

        $configurationStorage = $this->createMock(ApplicationConfigurationStorage::class);
        $configurationStorage
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($applicationConfiguration)
        ;

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);

        $subscriber = new SecuritySubscriber($configurationStorage, $tokenStorage, $authorizationChecker);

        return [
            $subscriber,
            $configurationStorage,
            $tokenStorage,
            $authorizationChecker,
        ];
    }

    private function createApplicationConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $applicationConfiguration = new ApplicationConfiguration();
        $applicationConfiguration->configureOptions($resolver);
        $applicationConfiguration->setParameters($resolver->resolve($configuration));

        return $applicationConfiguration;
    }
}
