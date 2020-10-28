<?php

namespace LAG\AdminBundle\Message\Registry;

use LAG\AdminBundle\Message\NotificationInterface;

class NotificationRegistry implements NotificationRegistryInterface
{
    private $notifications = [];

    public function add(NotificationInterface $notification): NotificationRegistryInterface
    {
        $this->notifications[] = $notification;

        return $this;
    }

    public function all(): iterable
    {
        foreach ($this->notifications as $notification) {
            yield $notification;
        }
    }

    public function count(): int
    {
        return count($this->notifications);
    }
}
