<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\MenuEvent;
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

    /**
     * HomeAction constructor.
     *
     * @param Twig_Environment                $twig
     * @param EventDispatcherInterface        $eventDispatcher
     * @param ApplicationConfigurationStorage $applicationConfigurationStorage
     */
    public function __construct(
        Twig_Environment $twig,
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
