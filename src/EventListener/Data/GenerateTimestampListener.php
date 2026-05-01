<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Data;

use LAG\AdminBundle\Entity\TimestampedResourceInterface;
use LAG\AdminBundle\Event\DataEvent;

final readonly class GenerateTimestampListener
{
    public function __invoke(DataEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof TimestampedResourceInterface) {
            return;
        }
        $now = new \DateTime();

        if ($data->getCreatedAt() === null) {
            $data->setCreatedAt($now);
        }
        $data->setUpdatedAt($now);
    }
}
