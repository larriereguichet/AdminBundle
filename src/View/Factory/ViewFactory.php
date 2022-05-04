<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Admin\View\AdminView;
use LAG\AdminBundle\Factory\FieldFactoryInterface;
use LAG\AdminBundle\Routing\Parameter\ParametersMapper;
use LAG\AdminBundle\Routing\Redirection\RedirectionUtils;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\View\RedirectView;
use LAG\AdminBundle\View\Template\Template;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;

class ViewFactory implements ViewFactoryInterface
{
    public function __construct(
        private FieldFactoryInterface $fieldFactory,
        private ApplicationConfiguration $applicationConfiguration,
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
        $fieldConfigurations = $actionConfiguration->getFields();
        $context = [
            'admin_name' => $actionConfiguration->getAdminName(),
            'action_name' => $actionConfiguration->getName(),
            'entity_class' => $admin->getConfiguration()->getEntityClass(),
        ];


        if (count($fieldConfigurations) > 0) {
            foreach ($actionConfiguration->getFields() as $name => $configuration) {
                $fields[$name] = $this->fieldFactory->create($name, $configuration, $context);
            }
        } else {
            $reflectionClass = new \ReflectionClass($admin->getEntityClass());

            foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                $fields[$reflectionProperty->getName()] = $this
                    ->fieldFactory
                    ->create($reflectionProperty->getName(), [], $context)
                ;
            }
//            $allowedItemActions = [];
//
//            if ($admin->getConfiguration()->hasAction('update')) {
//                $allowedItemActions['update'] = [];
//            }
//
//            if ($admin->getConfiguration()->hasAction('delete')) {
//                $allowedItemActions['delete'] = [];
//            }
//
//            if (count($allowedItemActions) > 0) {
//                $fields['_actions'] = $this->fieldFactory->create('_actions', [
//                    'type' => ActionCollectionField::class,
//                ], $context);
//            }
        }

        if ($request->isXmlHttpRequest()) {
            $template = $this->applicationConfiguration->get('ajax_template');
        } else {
            $template = $this->applicationConfiguration->get('base_template');
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
            $formViews,
        );
    }
}
