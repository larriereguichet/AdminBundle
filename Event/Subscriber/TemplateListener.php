<?php

namespace BlueBear\AdminBundle\Event\Subscriber;

use BlueBear\AdminBundle\Admin\Factory\AdminFactory;
use BlueBear\BaseBundle\Behavior\ContainerTrait;;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig_Environment;

class TemplateListener implements EventSubscriberInterface
{
    use ContainerTrait;

    /**
     * @var AdminFactory
     */
    protected $adminFactory;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

    /**
     * @param KernelEvent $event
     * @return array|null
     */
    public function onKernelRequest(KernelEvent $event)
    {
        // creating application configuration object
        $applicationConfig = $this
            ->adminFactory
            ->createApplicationFromConfiguration($this->container->getParameter('bluebear.admin.application'));
        // adding to twig globals (available on each twig template)
        $this->twig->addGlobal('config', $applicationConfig);
        $this->twig->addGlobal('routing', $this->container->get('bluebear.admin.routing'));
    }

    public function setAdminFactory(AdminFactory $adminFactory)
    {
        $this->adminFactory = $adminFactory;
    }

    public function setTwig(Twig_Environment $twigEngine)
    {
        $this->twig = $twigEngine;
    }
}
