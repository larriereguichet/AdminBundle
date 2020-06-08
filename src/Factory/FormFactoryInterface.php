<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface FormFactoryInterface
{
    /**
     * @param AdminInterface $admin
     * @param Request        $request
     * @param null           $entity
     *
     * @return FormInterface
     */
    public function createEntityForm(AdminInterface $admin, Request $request, $entity = null): FormInterface;

    /**
     * @param ActionInterface $action
     * @param Request         $request
     * @param                 $entity
     *
     * @return FormInterface
     */
    public function createDeleteForm(ActionInterface $action, Request $request, $entity): FormInterface;
}
