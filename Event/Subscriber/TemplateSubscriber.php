<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig_Environment;

class TemplateSubscriber implements EventSubscriberInterface
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var ApplicationConfiguration
     */
    protected $configuration;

    public function __construct(Twig_Environment $twig, ApplicationConfiguration $configuration)
    {
        $this->twig = $twig;
        $this->configuration = $configuration;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'kernelView',
        ];
    }

    /**
     * @param KernelEvent $event
     *
     * @return null
     */
    public function kernelView(KernelEvent $event)
    {
        $this->twig->addGlobal('config', $this->configuration);
    }
}
