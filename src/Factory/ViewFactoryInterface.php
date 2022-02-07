<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\View\AdminView;
use Symfony\Component\HttpFoundation\Request;

interface ViewFactoryInterface
{
    /**
     * Create a view for a given Admin and Action.
     */
    public function create(Request $request, AdminInterface $admin): AdminView;
}
