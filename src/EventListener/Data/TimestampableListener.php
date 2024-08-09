<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Data;

use LAG\AdminBundle\Entity\TimestampableInterface;
use LAG\AdminBundle\Event\DataEvent;

class TimestampableListener
{
    public function __invoke(DataEvent $event): void
    {
        $data = $event->getData();

        if ($data instanceof TimestampableInterface) {
            $data->setCreatedAt();
            $data->setUpdatedAt();
        }
    }
}
