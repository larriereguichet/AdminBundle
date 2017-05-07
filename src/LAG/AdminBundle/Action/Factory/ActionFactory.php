<?php

namespace LAG\AdminBundle\Action\Factory;

use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Event\ActionCreatedEvent;
use LAG\AdminBundle\Action\Event\ActionEvents;
use LAG\AdminBundle\Action\Event\BeforeConfigurationEvent;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\LAGAdminBundle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class allow to inject an Admin into a Controller.
 */
class ActionFactory
{
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
     * @param ConfigurationFactory $configurationFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ConfigurationFactory $configurationFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->configurationFactory = $configurationFactory;
        $this->eventDispatcher = $eventDispatcher;
    }
    
    /**
     * Inject an ActionConfiguration into an Action controller.
     *
     * @param mixed $controller
     * @param Request $request
     */
    public function injectConfiguration($controller, Request $request)
    {
        if (!$controller instanceof ActionInterface) {
            return;
        }
        
        if (!$controller->getAdmin() instanceof AdminInterface) {
            return;
        }
        // retrieve actions configuration
        $actionsConfiguration = $controller
            ->getAdmin()
            ->getConfiguration()
            ->getParameter('actions')
        ;
        $actionName = $request->get('_route_params')[LAGAdminBundle::REQUEST_PARAMETER_ACTION];
        
        // if the current action name is not supported, we do nothing
        if (!array_key_exists($actionName, $actionsConfiguration)) {
            return;
        }
        // BeforeConfigurationEvent allows users to override action configuration
        $event = new BeforeConfigurationEvent($actionName, $actionsConfiguration[$actionName], $controller->getAdmin());
        $this
            ->eventDispatcher
            ->dispatch(ActionEvents::BEFORE_CONFIGURATION, $event)
        ;
        
        // retrieve the current Action configuration
        $configuration = $this
            ->configurationFactory
            ->createActionConfiguration($actionName, $controller->getAdmin(), $event->getActionConfiguration());
        
        // allow users to listen after action creation
        $event = new ActionCreatedEvent($controller, $controller->getAdmin());
        $this
            ->eventDispatcher
            ->dispatch(
                ActionEvents::ACTION_CREATED, $event);
        
        // inject the Action to the controller
        $controller->setConfiguration($configuration);
    }
}
