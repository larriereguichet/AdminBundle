<?php

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Message\Registry\NotificationRegistryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MessageExtension extends AbstractExtension
{
    private NotificationRegistryInterface $registry;

    public function __construct(NotificationRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_notifications', [$this, 'getAdminNotifications']),
            new TwigFunction('admin_notifications_count', [$this, 'getAdminNotificationsCount']),
            new TwigFunction('admin_has_notifications', [$this, 'hasNotifications']),
        ];
    }

    public function getAdminNotifications(): iterable
    {
        return $this->registry->all();
    }

    public function getAdminNotificationsCount(): int
    {
        return $this->registry->count();
    }

    public function hasNotifications(): bool
    {
        return $this->getAdminNotificationsCount() > 0;
    }
}
