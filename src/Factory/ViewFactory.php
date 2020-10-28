<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Utils\RedirectionUtils;
use LAG\AdminBundle\View\AdminView;
use LAG\AdminBundle\View\RedirectView;
use LAG\AdminBundle\View\Template;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;

class ViewFactory implements ViewFactoryInterface
{
    private FieldFactoryInterface $fieldFactory;
    private ApplicationConfiguration $appConfig;
    private RedirectionUtils $redirectionUtils;

    public function __construct(
        FieldFactoryInterface $fieldFactory,
        ApplicationConfiguration $appConfig,
        RedirectionUtils $redirectionUtils
    ) {
        $this->fieldFactory = $fieldFactory;
        $this->appConfig = $appConfig;
        $this->redirectionUtils = $redirectionUtils;
    }

    public function create(Request $request, AdminInterface $admin): ViewInterface
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
