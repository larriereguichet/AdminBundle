<?php

namespace BlueBear\AdminBundle\Event\Subscriber;

use BlueBear\AdminBundle\Admin\Application\ApplicationConfiguration;
use BlueBear\BaseBundle\Behavior\ContainerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TemplateListener implements EventSubscriberInterface
{
    use ContainerTrait;

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
        $request = $event->getRequest();
        $twig = $this->container->get('twig');
        // creating application configuration object
        $applicationConfig = $this
            ->getContainer()
            ->get('bluebear.admin.factory')
            ->createApplicationFromConfiguration($this->container->getParameter('bluebear.admin.application'), $request);
        // adding to twig globals (available on each twig template)
        $twig->addGlobal('config', $applicationConfig);
    }
}