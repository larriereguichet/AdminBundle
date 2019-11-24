<?php

namespace LAG\AdminBundle\Admin\Helper;

use LAG\AdminBundle\Admin\AdminInterface;

interface AdminHelperInterface
{
    public function setCurrent(AdminInterface $admin): void;

    public function getCurrent(): ?AdminInterface;
}
