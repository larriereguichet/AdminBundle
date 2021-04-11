<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface AdminFormFactoryInterface
{
    /**
     * Create a form linked to an entity (for create and edit action for example).
     *
     * @param AdminInterface $admin
     * @param Request        $request
     * @param object|null    $data
     *
     * @return FormInterface
     */
    public function createEntityForm(AdminInterface $admin, Request $request, object $data = null): FormInterface;

    /**
     * Create the form to delete an entity.
     *
     * @param AdminInterface $admin
     * @param Request        $request
     * @param object         $data
     *
     * @return FormInterface
     */
    public function createDeleteForm(AdminInterface $admin, Request $request, object $data): FormInterface;
}
