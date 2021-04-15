<?php

namespace LAG\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HomeAction
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(): Response
    {
        return new Response($this->twig->render('@LAGAdmin/pages/home.html.twig'));
    }
}
