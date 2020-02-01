<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\BuildMenuEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

class HomeAction
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;

    /**
     * HomeAction constructor.
     */
    public function __construct(
        Environment $twig,
        EventDispatcherInterface $eventDispatcher,
        ApplicationConfigurationStorage $applicationConfigurationStorage
    ) {
        $this->twig = $twig;
        $this->eventDispatcher = $eventDispatcher;
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
    }

    /**
     * @return Response
     */
    public function __invoke()
    {
        $event = new BuildMenuEvent();
        $this->eventDispatcher->dispatch(Events::MENU, $event);

        $content = $this->twig->render($this->applicationConfiguration->getParameter('homepage_template'));

        return new Response($content);
    }
}
