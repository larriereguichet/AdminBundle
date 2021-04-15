<?php

namespace LAG\AdminBundle\Factory\Form;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;

interface FilterFormFactoryInterface
{
    public function create(AdminInterface $admin): FormInterface;
}
