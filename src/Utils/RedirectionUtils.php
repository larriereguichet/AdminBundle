<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Utils;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RedirectionUtils
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function isRedirectionRequired(AdminInterface $admin): bool
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

    public function getRedirectionUrl(AdminInterface $admin): string
    {
        if (!$this->isRedirectionRequired($admin)) {
            throw new Exception('The redirection is not required for the current action');
        }
        $request = $admin->getRequest();
        $configuration = $admin->getConfiguration();
        $actionName = 'edit';

        if ($request->get('submit_and_redirect') && $configuration->hasAction('list')) {
            $actionName = 'list';
        }
        // Retrieve the action configuration array. This is not an object as only the current action is instanciate
        $parameters = $admin->getConfiguration()->getActionRouteParameters($actionName);

        return $this->urlGenerator->generate($admin->getName(), $actionName, $parameters, $admin->getData());
    }

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

        if (!\array_key_exists('edit', $configuration->get('actions'))) {
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

        if (!\array_key_exists('list', $configuration->get('actions'))) {
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
