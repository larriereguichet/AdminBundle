<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\MenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class HomeAction
{
    /**
     * @var Twig_Environment
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

    public function __construct(
        Twig_Environment $twig,
        EventDispatcherInterface $eventDispatcher,
        ApplicationConfigurationStorage $applicationConfigurationStorage
    ) {
        $this->twig = $twig;
        $this->eventDispatcher = $eventDispatcher;
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
    }

    public function __invoke()
    {
        $event = new MenuEvent();
        $this->eventDispatcher->dispatch(AdminEvents::MENU, $event);

        $content = $this->twig->render($this->applicationConfiguration->getParameter('homepage_template'));

        return new Response($content);
    }
}
