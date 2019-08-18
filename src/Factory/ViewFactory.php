<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Routing\RoutingLoader;
use LAG\AdminBundle\Utils\RedirectionUtils;
use LAG\AdminBundle\View\RedirectView;
use LAG\AdminBundle\View\View;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class ViewFactory
{
    /**
     * @var FieldFactory
     */
    private $fieldFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ApplicationConfigurationStorage
     */
    private $storage;

    /**
     * ViewFactory constructor.
     *
     * @param FieldFactory                    $fieldFactory
     * @param RouterInterface                 $router
     * @param ApplicationConfigurationStorage $storage
     */
    public function __construct(
        FieldFactory $fieldFactory,
        RouterInterface $router,
        ApplicationConfigurationStorage $storage
    ) {
        $this->fieldFactory = $fieldFactory;
        $this->router = $router;
        $this->storage = $storage;
    }

    /**
     * Create a view for a given Admin and Action.
     *
     * @param Request             $request
     * @param string              $actionName
     * @param string              $adminName
     * @param AdminConfiguration  $adminConfiguration
     * @param ActionConfiguration $actionConfiguration
     * @param mixed               $entities
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
    ): ViewInterface {
        if (key_exists('entity', $forms)) {
            $form = $forms['entity'];

            if ($this->shouldRedirect($form, $request, $adminConfiguration)) {
                return $this->createRedirection(
                    $actionName,
                    $adminName,
                    $request,
                    $adminConfiguration,
                    $actionConfiguration,
                    $form
                );
            }
        }
        $fields = $this
            ->fieldFactory
            ->createFields($actionConfiguration);
        $formViews = [];

        foreach ($forms as $identifier => $form) {
            $formViews[$identifier] = $form->createView();
        }

        if ($request->isXmlHttpRequest()) {
            $template = $this->storage->getConfiguration()->get('ajax_template');
        } else {
            $template = $this->storage->getConfiguration()->get('base_template');
        }

        $view = new View(
            $actionName,
            $adminName,
            $actionConfiguration,
            $adminConfiguration,
            $template,
            $fields,
            $formViews
        );
        $view->setEntities($entities);

        return $view;
    }

    public function createRedirection(
        $actionName,
        $adminName,
        Request $request,
        AdminConfiguration $adminConfiguration,
        ActionConfiguration $actionConfiguration,
        FormInterface $form
    ): RedirectView {
        if (RedirectionUtils::shouldRedirectToEdit($form, $request, $adminConfiguration)) {
            $view = new RedirectView(
                $actionName,
                $adminName,
                $actionConfiguration,
                $adminConfiguration
            );
            $view->setUrl($request->getUri());

            return $view;
        }

        if (RedirectionUtils::shouldRedirectToList($form, $request, $adminConfiguration)) {
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

        throw new Exception('Unable to find a url to redirect');
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
    private function shouldRedirect(FormInterface $form, Request $request, AdminConfiguration $configuration): bool
    {
        if (RedirectionUtils::shouldRedirectToEdit($form, $request, $configuration)) {
            return true;
        }

        if (RedirectionUtils::shouldRedirectToList($form, $request, $configuration)) {
            return true;
        }

        return false;
    }
}
