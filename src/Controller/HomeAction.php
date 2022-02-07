<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HomeAction
{
    public function __construct(
        private Environment $twig,
    )
    {
    }

    public function __invoke(): Response
    {
        return new Response($this->twig->render('@LAGAdmin/pages/home.html.twig'));
    }
}
