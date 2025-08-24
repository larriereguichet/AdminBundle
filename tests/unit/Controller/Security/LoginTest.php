<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Controller\Security;

use LAG\AdminBundle\Controller\Security\Login;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

final class LoginTest extends TestCase
{
    private Login $controller;
    private MockObject $authenticationUtils;
    private MockObject $environment;

    #[Test]
    public function itReturnsAResponse(): void
    {
        $authenticationException = new AuthenticationException('Some error');

        $this->authenticationUtils
            ->expects($this->once())
            ->method('getLastAuthenticationError')
            ->willReturn($authenticationException)
        ;
        $this->authenticationUtils
            ->expects($this->once())
            ->method('getLastUsername')
            ->willReturn('my_username')
        ;
        $this->environment
            ->expects($this->once())
            ->method('render')
            ->with('@LAGAdmin/security/login.html.twig', [
                'error' => $authenticationException,
                'username' => 'my_username',
            ])
            ->willReturn('some html content')
        ;

        $response = $this->controller->__invoke();

        self::assertEquals('some html content', $response->getContent());
    }

    protected function setUp(): void
    {
        $this->authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $this->environment = $this->createMock(Environment::class);
        $this->controller = new Login(
            $this->authenticationUtils,
            $this->environment,
        );
    }
}
