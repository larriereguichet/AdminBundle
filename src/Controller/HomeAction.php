<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\MenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
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
        $event = new MenuEvent();
        $this->eventDispatcher->dispatch(Events::MENU, $event);

        $content = $this->twig->render($this->applicationConfiguration->getParameter('homepage_template'));

        return new Response($content);
    }
}
