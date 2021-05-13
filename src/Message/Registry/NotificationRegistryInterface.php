<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Message\Registry;

use LAG\AdminBundle\Message\NotificationInterface;

interface NotificationRegistryInterface
{
    public function add(NotificationInterface $notification): self;

    public function all(): iterable;

    public function count(): int;
}
