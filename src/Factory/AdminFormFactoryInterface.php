<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface AdminFormFactoryInterface
{
    /**
     * Create a form linked to an entity (for create and edit action for example).
     */
    public function createEntityForm(AdminInterface $admin, Request $request, object $data = null): FormInterface;

    /**
     * Create the form to delete an entity.
     */
    public function createDeleteForm(AdminInterface $admin, Request $request, object $data): FormInterface;
}
