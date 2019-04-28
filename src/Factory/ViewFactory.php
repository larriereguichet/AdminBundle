<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Routing\RoutingLoader;
use LAG\AdminBundle\View\RedirectView;
use LAG\AdminBundle\View\View;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class ViewFactory
{
    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var FieldFactory
     */
    private $fieldFactory;

    /**
     * @var MenuFactory
     */
    private $menuFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * ViewFactory constructor.
     *
     * @param ConfigurationFactory $configurationFactory
     * @param FieldFactory         $fieldFactory
     * @param MenuFactory          $menuFactory
     * @param RouterInterface      $router
     */
    public function __construct(
        ConfigurationFactory $configurationFactory,
        FieldFactory $fieldFactory,
        MenuFactory $menuFactory,
        RouterInterface $router
    ) {
        $this->configurationFactory = $configurationFactory;
        $this->fieldFactory = $fieldFactory;
        $this->menuFactory = $menuFactory;
        $this->router = $router;
    }

    /**
     * Create a view for a given Admin and Action.
     *
     * @param Request             $request
     * @param string              $actionName
     * @param string              $adminName
     * @param AdminConfiguration  $adminConfiguration
     * @param ActionConfiguration $actionConfiguration
     * @param                     $entities
     * @param FormInterface[]     $forms
     *
     * @return ViewInterface
     */
    public function create(
        Request $request,
        $actionName,
        $adminName,
        AdminConfiguration $adminConfiguration,
        ActionConfiguration $actionConfiguration,
        $entities,
        array $forms = []
    ) {
        if (key_exists('entity', $forms)) {
            $form = $forms['entity'];

            if ($this->shouldRedirect($form, $request, $adminConfiguration)) {
                $routeName = RoutingLoader::generateRouteName(
                    $adminName,
                    'list',
                    $adminConfiguration->getParameter('routing_name_pattern')
                );
                $url = $this->router->generate($routeName);
                $view = new RedirectView(
                    $actionName,
                    $adminName,
                    $actionConfiguration,
                    $adminConfiguration
                );
                $view->setUrl($url);

                return $view;
            }
        }
        $fields = $this
            ->fieldFactory
            ->createFields($actionConfiguration)
        ;
        $formViews = [];

        foreach ($forms as $identifier => $form) {
            $formViews[$identifier] = $form->createView();
        }

        $view = new View(
            $actionName,
            $adminName,
            $actionConfiguration,
            $adminConfiguration,
            $fields,
            $formViews
        );
        $view->setEntities($entities);

        return $view;
    }

    /**
     * Return true if a redirection view should be created.
     *
     * @param FormInterface      $form
     * @param Request            $request
     * @param AdminConfiguration $configuration
     *
     * @return bool
     */
    private function shouldRedirect(FormInterface $form, Request $request, AdminConfiguration $configuration)
    {
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
