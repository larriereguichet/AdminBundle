<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Security;

use LAG\AdminBundle\Form\Type\Security\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

final readonly class Login
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private AuthenticationUtils $authenticationUtils,
        private Environment $environment,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();

        $form = $this->formFactory->create(LoginType::class);
        $form->handleRequest($request);

        return new Response($this->environment->render('@LAGAdmin/security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]));
    }
}
