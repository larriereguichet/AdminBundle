<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Admin\Factory\AdminFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class AdminSubscriber implements EventSubscriberInterface
{
    /**
     * @var AdminFactory
     */
    protected $adminFactory;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'kernelRequest'
        ];
    }

    /**
     * AdminSubscriber constructor.
     *
     * @param AdminFactory $adminFactory
     */
    public function __construct(AdminFactory $adminFactory)
    {
        $this->adminFactory = $adminFactory;
    }

    /**
     * Init admin factory on kernel request.
     */
    public function kernelRequest()
    {
        // init admin factory
        $this->adminFactory->init();
    }
}
