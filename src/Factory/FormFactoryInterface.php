<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface FormFactoryInterface
{
    /**
     * @param null           $entity
     */
    public function createEntityForm(AdminInterface $admin, Request $request, $entity = null): FormInterface;

    /**
     * @param                 $entity
     */
    public function createDeleteForm(ActionInterface $action, Request $request, $entity): FormInterface;
}
