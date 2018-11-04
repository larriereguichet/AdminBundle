<?php

namespace LAG\AdminBundle\Event\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\EntityEvent;
use LAG\AdminBundle\Event\MenuEvent;
use LAG\AdminBundle\Event\ViewEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\ActionFactory;
use LAG\AdminBundle\Factory\DataProviderFactory;
use LAG\AdminBundle\Factory\ViewFactory;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Utils\StringUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var Session
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::HANDLE_REQUEST => 'handleRequest',
            AdminEvents::VIEW => 'createView',
            AdminEvents::ENTITY_LOAD => 'loadEntities',
            AdminEvents::ENTITY_SAVE => 'saveEntity',
        ];
    }

    /**
     * AdminSubscriber constructor.
     *
     * @param ActionFactory            $actionFactory
     * @param ViewFactory              $viewFactory
     * @param DataProviderFactory      $dataProviderFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param SessionInterface         $session
     * @param TranslatorInterface      $translator
     */
    public function __construct(
        ActionFactory $actionFactory,
        ViewFactory $viewFactory,
        DataProviderFactory $dataProviderFactory,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session,
        TranslatorInterface $translator
    ) {
        $this->actionFactory = $actionFactory;
        $this->viewFactory = $viewFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->dataProviderFactory = $dataProviderFactory;
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * Define the current action according to the routing configuration.
     *
     * @param AdminEvent $event
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
        $action = $this->actionFactory->create($actionName, $admin->getName(), $admin->getConfiguration());

        $event->setAction($action);
    }

    /**
     * Create a view using the view factory.
     *
     * @param ViewEvent $event
     */
    public function createView(ViewEvent $event)
    {
        $admin = $event->getAdmin();
        $action = $admin->getAction();
        $menuEvent = new MenuEvent($admin->getAction()->getConfiguration()->getParameter('menus'));
        $this->eventDispatcher->dispatch(AdminEvents::MENU, $menuEvent);

        $view = $this->viewFactory->create(
            $event->getRequest(),
            $action->getName(),
            $admin->getName(),
            $admin->getConfiguration(),
            $action->getConfiguration(),
            $admin->getEntities(),
            $admin->getForms()
        );

        $event->setView($view);
    }

    /**
     * Load entities into the event data to pass them to the Admin.
     *
     * @param EntityEvent $event
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
        } else if (LAGAdminBundle::LOAD_STRATEGY_MULTIPLE === $strategy) {
            $entities = $dataProvider->getCollection($admin, $event->getFilters());
            $event->setEntities($entities);
        } else if (LAGAdminBundle::LOAD_STRATEGY_UNIQUE === $strategy) {
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
     *
     * @param EntityEvent $event
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
        $message = $this->translateMessage('save_success', $admin->getName(), $configuration);

        $this
            ->session
            ->getFlashBag()
            ->add('success', $message)
        ;
    }

    private function translateMessage(string $message, string $adminName, AdminConfiguration $configuration): string
    {
        $pattern = $configuration->getParameter('translation_pattern');
        $message = StringUtils::getTranslationKey($pattern, $adminName, $message);

        return $this->translator->trans($message);
    }
}
