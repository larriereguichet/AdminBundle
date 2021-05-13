<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Form\Type\Security\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class LoginAction
{
    private FormFactoryInterface $formFactory;
    private Environment $environment;

    public function __construct(FormFactoryInterface $formFactory, Environment $environment)
    {
        $this->formFactory = $formFactory;
        $this->environment = $environment;
    }

    public function __invoke(Request $request, string $template): Response
    {
        $form = $this->formFactory->create(LoginType::class);
        $form->handleRequest($request);

        return new Response($this->environment->render($request->attributes->get('template'), [
            'form' => $form->createView(),
        ]));
    }
}
