<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface AdminFormFactoryInterface
{
    public function createEntityForm(AdminInterface $admin, Request $request, object $data = null): FormInterface;

    public function createDeleteForm(AdminInterface $admin, Request $request, object $data): FormInterface;
}
