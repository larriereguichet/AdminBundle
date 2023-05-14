<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Data;

use LAG\AdminBundle\Entity\SluggableInterface;
use LAG\AdminBundle\Event\Events\DataEvent;

class SlugListener
{
    public function __invoke(DataEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof SluggableInterface || !$data->getSlugSource()) {
            return;
        }
        $data->generateSlug($data->getSlugSource());
    }
}
