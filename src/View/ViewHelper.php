<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use Symfony\Component\HttpFoundation\Request;

class ViewHelper
{
    public function shouldRedirect(
        Request $request,
        AdminConfiguration $configuration,
        AdminInterface $admin,
        ActionInterface $action,
        string $formName = ''
    ) {
        if (!$this->isFormValid($admin, $configuration, $formName)) {
            return false;
        }

        // When the create form is submitted, the user should be redirected to the edit action after saving the form
        // data
        if ('create' === $action->getName()) {
            return true;
        }

        // When the delete form is submitted, we should redirect to the list action
        if ('delete' === $action->getName()) {
            return true;
        }

        if (!$request->get('submit_and_redirect')) {
            return false;
        }

        if ('submit_and_redirect' !== $request->get('submit_and_redirect')) {
            return false;
        }

        return true;
    }

    private function isFormValid(AdminInterface $admin, AdminConfiguration $configuration, string $formName): bool
    {
        if (!$admin->hasForm($formName)) {
            return false;
        }
        $form = $admin->getForm($formName);

        if (!$form->isSubmitted()) {
            return false;
        }

        if (!$form->isValid()) {
            return false;
        }

        if (!\array_key_exists('list', $configuration->getActions())) {
            return false;
        }

        return true;
    }
}
