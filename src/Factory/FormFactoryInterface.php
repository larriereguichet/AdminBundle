<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;

interface FormFactoryInterface
{
    public function createEntityForm(AdminInterface $admin, $entity = null): FormInterface;

    public function createDeleteForm(ActionInterface $action, $entity): FormInterface;
}
