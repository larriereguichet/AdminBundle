<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Factory\FieldFactoryInterface;
use LAG\AdminBundle\Routing\Parameter\ParametersMapper;
use LAG\AdminBundle\Routing\Redirection\RedirectionUtils;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\View\AdminView;
use LAG\AdminBundle\View\RedirectView;
use LAG\AdminBundle\View\Template\Template;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;

class ViewFactory implements ViewFactoryInterface
{
    public function __construct(
        private FieldFactoryInterface $fieldFactory,
        private ApplicationConfiguration $appConfig,
        private RouteNameGeneratorInterface $routeNameGenerator,
    ) {
    }

    public function create(Request $request, AdminInterface $admin): ViewInterface
    {
        if (RedirectionUtils::isRedirectionRequired($admin)) {
            $targetRoute = $admin->getAction()->getConfiguration()->getTargetRoute();
            $targetRouteParameters = $admin->getAction()->getConfiguration()->getTargetRouteParameters();

            if ($admin->getConfiguration()->hasAction($targetRoute)) {
                $targetRoute = $this->routeNameGenerator->generateRouteName($admin->getName(), $targetRoute);
            }

            return new RedirectView(
                $targetRoute,
                (new ParametersMapper())->map($admin->getData(), $targetRouteParameters),
            );
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
