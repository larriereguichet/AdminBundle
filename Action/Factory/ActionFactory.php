<?php

namespace LAG\AdminBundle\Action\Factory;

use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Action\Event\ActionCreatedEvent;
use LAG\AdminBundle\Action\Event\ActionCreateEvent;
use LAG\AdminBundle\Action\Event\ActionEvents;
use LAG\AdminBundle\Action\Event\BeforeConfigurationEvent;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Factory\FilterFactory;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\Field\Factory\FieldFactory;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ActionFactory
{
    /**
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * ActionFactory constructor.
     *
     * @param FieldFactory $fieldFactory
     * @param FilterFactory $filterFactory
     * @param ConfigurationFactory $configurationFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        FieldFactory $fieldFactory,
        FilterFactory $filterFactory,
        ConfigurationFactory $configurationFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->fieldFactory = $fieldFactory;
        $this->filterFactory = $filterFactory;
        $this->configurationFactory = $configurationFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Create an Action from configuration values.
     *
     * @param string $actionName
     * @param array $configuration
     * @param AdminInterface $admin
     *
     * @return Action
     */
    public function create($actionName, array $configuration, AdminInterface $admin)
    {
        // before configuration event allows users to override action configuration
        $this->dispatch(
            ActionEvents::BEFORE_CONFIGURATION,
            $event = new BeforeConfigurationEvent($actionName, $configuration, $admin)
        );

        // create action configuration object
        $actionConfiguration = $this
            ->configurationFactory
            ->createActionConfiguration($actionName, $admin, $event->getActionConfiguration());

        // action create event allows users to know what configuration will be passed to the action
        $this->dispatch(
            ActionEvents::ACTION_CREATE,
            new ActionCreateEvent($actionName, $configuration, $admin)
        );

        // create action
        $action = new Action($actionName, $actionConfiguration);

        // adding fields items to actions
        foreach ($actionConfiguration->getParameter('fields') as $fieldName => $fieldConfiguration) {
            $field = $this
                ->fieldFactory
                ->create($fieldName, $fieldConfiguration);
            $action->addField($field);
        }

        // adding filters to the action
        foreach ($actionConfiguration->getParameter('filters') as $fieldName => $filterConfiguration) {
            $filter = $this
                ->filterFactory
                ->create($fieldName, $filterConfiguration);
            $action->addFilter($filter);
        }
        // add the action to the admin
        $admin->addAction($action);

        // allow users to listen after action creation
        $this->dispatch(
            ActionEvents::ACTION_CREATED,
            new ActionCreatedEvent($action, $admin)
        );

        return $action;
    }

    /**
     * Dispatch an event using the main event dispatcher.
     *
     * @param string $name
     * @param Event $event
     */
    protected function dispatch($name, Event $event)
    {
        $this
            ->eventDispatcher
            ->dispatch($name, $event);
    }
}
