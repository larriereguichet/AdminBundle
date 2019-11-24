<?php

namespace LAG\AdminBundle\Admin\Helper;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Exception\Exception;

class AdminHelper implements AdminHelperInterface
{
    private $frozen = false;

    /**
     * @var AdminInterface
     */
    private $admin;

    public function setCurrent(AdminInterface $admin): void
    {
        if ($this->frozen) {
            throw new Exception('The current admin cannot be set twice in a request');
        }
        $this->admin = $admin;
        $this->frozen = true;
    }

    public function getCurrent(): ?AdminInterface
    {
        return $this->admin;
    }
}
