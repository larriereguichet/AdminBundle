<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use Symfony\Contracts\EventDispatcher\Event;

class DataEvent extends Event
{
    public function __construct(
        private readonly mixed $data,
    ) {
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
