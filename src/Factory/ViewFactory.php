<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Utils\RedirectionUtils;
use LAG\AdminBundle\View\AdminView;
use LAG\AdminBundle\View\RedirectView;
use LAG\AdminBundle\View\Template;
use Symfony\Component\HttpFoundation\Request;

class ViewFactory implements ViewFactoryInterface
{
    public function __construct(
        private FieldFactoryInterface $fieldFactory,
        private ApplicationConfiguration $appConfig,
        private RedirectionUtils $redirectionUtils
    ) {
    }

    public function create(Request $request, AdminInterface $admin): AdminView
    {
        if ($this->redirectionUtils->isRedirectionRequired($admin)) {
            return new RedirectView($this->redirectionUtils->getRedirectionUrl($admin));
        }
        $actionConfiguration = $admin->getAction()->getConfiguration();
        $fields = [];
        $context = [
            'admin_name' => $actionConfiguration->getAdminName(),
            'action_name' => $actionConfiguration->getName(),
            'entity_class' => $admin->getConfiguration()->getEntityClass(),
        ];

        foreach ($actionConfiguration->getFields() as $name => $configuration) {
            $fields[$name] = $this->fieldFactory->create($name, $configuration, $context);
        }

        if ($request->isXmlHttpRequest()) {
            $template = $this->appConfig->get('ajax_template');
        } else {
            $template = $this->appConfig->get('base_template');
        }
        $template = new Template($actionConfiguration->getTemplate(), $template);
        $fieldViews = [];

        foreach ($fields as $name => $field) {
            $fieldViews[$name] = $field->createView();
        }
        $formViews = [];

        foreach ($admin->getForms() as $identifier => $form) {
            $formViews[$identifier] = $form->createView();
        }

        return new AdminView(
            $admin,
            $template,
            $fieldViews,
            $formViews
        );
    }
}
