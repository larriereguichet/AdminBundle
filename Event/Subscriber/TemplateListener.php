<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Admin\Factory\AdminFactory;
use BlueBear\BaseBundle\Behavior\ContainerTrait;
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
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    /**
     * @param KernelEvent $event
     *
     * @return array|null
     */
    public function onKernelRequest(KernelEvent $event)
    {
        // adding to twig globals (available on each twig template)
        // TODO inject right parameters instead of container
        $this->twig->addGlobal('config', $this->container->get('lag.admin.application'));
        $this->twig->addGlobal('routing', $this->container->get('lag.admin.routing'));
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
