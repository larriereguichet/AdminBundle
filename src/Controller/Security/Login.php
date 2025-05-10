<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

final readonly class Login
{
    public function __construct(
        private AuthenticationUtils $authenticationUtils,
        private Environment $environment,
    ) {
    }

    public function __invoke(): Response
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return new Response($this->environment->render('@LAGAdmin/security/login.html.twig', [
            'error' => $error,
            'username' => $lastUsername,
        ]));
    }
}
