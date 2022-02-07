<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Redirection;

use LAG\AdminBundle\Admin\AdminInterface;

class RedirectionUtils
{
    public static function isRedirectionRequired(AdminInterface $admin): bool
    {
        // Redirection are required when a form is submitted and valid
        foreach ($admin->getForms() as $formName => $form) {
            if ($formName === 'filter') {
                continue;
            }

            if ($form->isSubmitted() && $form->isValid()) {
                return true;
            }
        }

        return false;
    }
}
