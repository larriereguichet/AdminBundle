<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin\Helper;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Exception\Exception;

class AdminHelper implements AdminHelperInterface
{
    private bool $frozen = false;
    private AdminInterface $admin;

    public function setAdmin(AdminInterface $admin): void
    {
        if ($this->frozen) {
            throw new Exception('The current admin cannot be set twice in a request');
        }
        $this->admin = $admin;
        $this->frozen = true;
    }

    public function getAdmin(): AdminInterface
    {
        if (!$this->hasAdmin()) {
            throw new Exception('No admin has been set yet.');
        }

        return $this->admin;
    }

    public function hasAdmin(): bool
    {
        return isset($this->admin);
    }
}
