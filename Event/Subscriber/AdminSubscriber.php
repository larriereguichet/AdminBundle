<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Admin\Factory\AdminFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AdminSubscriber implements EventSubscriberInterface
{
    /**
     * @var AdminFactory
     */
    protected $adminFactory;

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'kernelRequest'
        ];
    }

    public function __construct(AdminFactory $adminFactory)
    {
        $this->adminFactory = $adminFactory;
    }

    public function kernelRequest(KernelEvent $event)
    {
        // init admin factory
        $this->adminFactory->init();
    }
}
