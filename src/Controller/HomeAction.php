<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HomeAction
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;

    /**
     * HomeAction constructor.
     */
    public function __construct(
        Environment $twig,
        ApplicationConfigurationStorage $applicationConfigurationStorage
    ) {
        $this->twig = $twig;
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
    }

    /**
     * @return Response
     */
    public function __invoke()
    {
        $content = $this->twig->render($this->applicationConfiguration->getParameter('homepage_template'));

        return new Response($content);
    }
}
