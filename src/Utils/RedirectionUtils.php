<?php

namespace LAG\AdminBundle\Utils;

use LAG\AdminBundle\Configuration\AdminConfiguration;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RedirectionUtils
{
    public static function shouldRedirectToEdit(
        FormInterface $form,
        Request $request,
        AdminConfiguration $configuration
    ): bool {
        if (!$form->isSubmitted()) {
            return false;
        }

        if (!$form->isValid()) {
            return false;
        }

        if ($request->get('submit_and_redirect')) {
            return false;
        }

        if (!key_exists('edit', $configuration->getParameter('actions'))) {
            return false;
        }

        return true;
    }

    public static function shouldRedirectToList(
        FormInterface $form,
        Request $request,
        AdminConfiguration $configuration
    ): bool {
        if (!$form->isSubmitted()) {
            return false;
        }

        if (!$form->isValid()) {
            return false;
        }

        if (!key_exists('list', $configuration->getParameter('actions'))) {
            return false;
        }

        if (!$request->get('submit_and_redirect')) {
            return false;
        }

        if ('submit_and_redirect' !== $request->get('submit_and_redirect')) {
            return false;
        }

        return true;
    }
}
