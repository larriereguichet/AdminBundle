<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::HANDLE_FORM => 'handleForm',
        ];
    }

    public function handleForm(FormEvent $event)
    {



    }
}
