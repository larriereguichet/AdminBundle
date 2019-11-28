<?php

namespace LAG\AdminBundle\Event\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\AdminEvent;
use LAG\AdminBundle\Event\Events\EntityEvent;
use LAG\AdminBundle\Event\Events\MenuEvent;
use LAG\AdminBundle\Event\Events\ViewEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\ActionFactory;
use LAG\AdminBundle\Factory\DataProviderFactory;
use LAG\AdminBundle\Factory\ViewFactory;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Routing\RoutingLoader;
use LAG\AdminBundle\Utils\TranslationUtils;
use LAG\AdminBundle\View\RedirectView;
use LAG\AdminBundle\View\ViewHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminSubscriber implements EventSubscriberInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var ViewFactory
     */
    private $viewFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var DataProviderFactory
     */
    private $dataProviderFactory;

    /**
     * @var Session|SessionInterface
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var AdminHelperInterface
     */
    private $helper;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::ADMIN_HANDLE_REQUEST => 'handleRequest',
            Events::ADMIN_VIEW => 'createView',
            Events::ENTITY_LOAD => 'loadEntities',
            Events::ENTITY_SAVE => 'saveEntity',
        ];
    }

    /**
     * AdminSubscriber constructor.
     */
    public function __construct(
        ActionFactory $actionFactory,
        ViewFactory $viewFactory,
        DataProviderFactory $dataProviderFactory,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session,
        TranslatorInterface $translator,
        RouterInterface $router,
        AdminHelperInterface $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->viewFactory = $viewFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->dataProviderFactory = $dataProviderFactory;
        $this->session = $session;
        $this->translator = $translator;
        $this->router = $router;
        $this->helper = $helper;
    }

    /**
     * Define the current action according to the routing configuration.
     *
     * @throws Exception
     */
    public function handleRequest(AdminEvent $event)
    {
        $actionName = $event->getRequest()->get('_action');

        if (null === $actionName) {
            throw new Exception('The _action was not found in the request');
        }
        $admin = $event->getAdmin();
        $this->helper->setCurrent($admin);
        $action = $this->actionFactory->create($actionName, $admin->getName(), $admin->getConfiguration());

        $event->setAction($action);
    }

    /**
     * Create a view using the view factory.
     */
    public function createView(ViewEvent $event)
    {
        $admin = $event->getAdmin();
        $action = $admin->getAction();
        $menuEvent = new MenuEvent($admin->getAction()->getConfiguration()->getParameter('menus'));
        $this->eventDispatcher->dispatch(Events::MENU, $menuEvent);
        $formName = '';

        // The form name is different according to the current action
        if ('edit' === $action->getName()) {
            $formName = 'entity';
        } elseif ('create' === $action->getName()) {
            $formName = 'entity';
        } elseif ('delete' === $action->getName()) {
            $formName = 'delete';
        }
        $viewHelper = new ViewHelper();

        if ($viewHelper->shouldRedirect($event->getRequest(), $admin->getConfiguration(), $admin, $action, $formName)) {
            $url = $this->getRedirectionUrl(
                $admin,
                $action,
                $admin->getConfiguration(),
                $event->getRequest()
            );

            $view = new RedirectView(
                $action->getName(),
                $admin->getName(),
                $action->getConfiguration(),
                $admin->getConfiguration()
            );
            $view->setUrl($url);
        } else {
            $view = $this->viewFactory->create(
                $event->getRequest(),
                $action->getName(),
                $admin->getName(),
                $admin->getConfiguration(),
                $action->getConfiguration(),
                $admin->getEntities(),
                $admin->getForms()
            );
        }
        $event->setView($view);
    }

    /**
     * Load entities into the event data to pass them to the Admin.
     *
     * @throws Exception
     */
    public function loadEntities(EntityEvent $event)
    {
        $admin = $event->getAdmin();
        $configuration = $admin->getConfiguration();
        $actionConfiguration = $admin->getAction()->getConfiguration();

        $dataProvider = $this->dataProviderFactory->get($configuration->getParameter('data_provider'));
        $strategy = $actionConfiguration->getParameter('load_strategy');
        $class = $configuration->getParameter('entity');

        if (LAGAdminBundle::LOAD_STRATEGY_NONE === $strategy) {
            return;
        } elseif (LAGAdminBundle::LOAD_STRATEGY_MULTIPLE === $strategy) {
            $entities = $dataProvider->getCollection($admin, $event->getFilters());
            $event->setEntities($entities);
        } elseif (LAGAdminBundle::LOAD_STRATEGY_UNIQUE === $strategy) {
            $requirements = $actionConfiguration->getParameter('route_requirements');
            $identifier = null;

            foreach ($requirements as $name => $requirement) {
                if (null !== $event->getRequest()->get($name)) {
                    $identifier = $event->getRequest()->get($name);
                    break;
                }
            }

            if (null === $identifier) {
                throw new Exception('Unable to find a identifier for the class "'.$class.'"');
            }
            $entity = $dataProvider->get($admin, $identifier);

            $event->setEntities(new ArrayCollection([
                $entity,
            ]));
        }
    }

    /**
     * Save an entity.
     */
    public function saveEntity(EntityEvent $event)
    {
        $admin = $event->getAdmin();
        $configuration = $admin->getConfiguration();

        // Save the entity changes using the configured data provider
        $dataProvider = $this
            ->dataProviderFactory
            ->get($configuration->getParameter('data_provider'))
        ;
        $dataProvider->save($admin);

        // Inform the user that the save is successful
        $message = $this->translateMessage('saved', $admin->getName(), $configuration);

        $this
            ->session
            ->getFlashBag()
            ->add('success', $message)
        ;
    }

    private function translateMessage(string $message, string $adminName, AdminConfiguration $configuration): string
    {
        $pattern = $configuration->getParameter('translation_pattern');
        $message = TranslationUtils::getTranslationKey($pattern, $adminName, $message);

        return $this->translator->trans($message);
    }

    /**
     * Return the url where the user should be redirected to. An exception will be thrown if no url can be generated.
     *
     * @throws Exception
     */
    private function getRedirectionUrl(
        AdminInterface $admin,
        ActionInterface $action,
        AdminConfiguration $configuration,
        Request $request
    ): string {
        if ('edit' === $action->getName()) {
            $routeName = RoutingLoader::generateRouteName(
                $admin->getName(),
                'list',
                $configuration->get('routing_name_pattern')
            );
            $url = $this->router->generate($routeName);

            return $url;
        }

        // When the create form is submitted, the user should be redirected to the edit action after saving the form
        // data
        if ('create' === $action->getName()) {
            $targetAction = 'list';
            $routeParameters = [];

            if (!$request->get('submit_and_redirect')) {
                $targetAction = 'edit';
                $routeParameters = [
                    'id' => $admin->getEntities()->first()->getId(),
                ];
            }
            $routeName = RoutingLoader::generateRouteName(
                $admin->getName(),
                $targetAction,
                $configuration->get('routing_name_pattern')
            );
            $url = $this->router->generate($routeName, $routeParameters);

            return $url;
        }

        if ('delete' === $action->getName()) {
            $routeName = RoutingLoader::generateRouteName(
                $admin->getName(),
                'list',
                $configuration->get('routing_name_pattern')
            );
            $url = $this->router->generate($routeName);

            return $url;
        }

        throw new Exception('Unable to generate an redirection url for the current action');
    }
}
