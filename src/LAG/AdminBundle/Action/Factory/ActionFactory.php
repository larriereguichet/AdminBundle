<?php

namespace LAG\AdminBundle\Action\Factory;

use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Event\ActionCreatedEvent;
use LAG\AdminBundle\Action\Event\ActionEvents;
use LAG\AdminBundle\Action\Event\BeforeConfigurationEvent;
use LAG\AdminBundle\Action\Registry\Registry;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\LAGAdminBundle;
use LogicException;
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
     * @var Registry
     */
    protected $registry;
    
    /**
     * ActionFactory constructor.
     *
     * @param ConfigurationFactory     $configurationFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param Registry                 $registry
     */
    public function __construct(
        ConfigurationFactory $configurationFactory,
        EventDispatcherInterface $eventDispatcher,
        Registry $registry
    ) {
        $this->configurationFactory = $configurationFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->registry = $registry;
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
            ->create(
                $actionName,
                $controller->getAdmin()->getName(),
                $controller->getAdmin()->getConfiguration(),
                $event->getActionConfiguration()
            )
        ;
        
        // allow users to listen after action creation
        $event = new ActionCreatedEvent($controller, $controller->getAdmin());
        $this
            ->eventDispatcher
            ->dispatch(
                ActionEvents::ACTION_CREATED, $event)
        ;
        
        // inject the Action to the controller
        $controller->setConfiguration($configuration);
    }
    
    /**
     * @param $adminName
     * @param array $configurations
     *
     * @return array
     */
    public function getActions($adminName, array $configurations)
    {
        $actions = [];
        
        foreach ($configurations['actions'] as $name => $configuration) {
            if (null !== $configuration && key_exists('service', $configuration)) {
                // if a service key is defined, take it
                $serviceId = $configuration['service'];
            } else {
                // if no service key was provided, we take the default action service
                $serviceId = $this->getActionServiceId($name, $adminName);
            }
            $action = $this
                ->registry
                ->get($serviceId)
            ;
            $actions[$name] = $action;
        }
        
        return $actions;
    }
    
    /**
     * @param $name
     * @param $adminName
     *
     * @return string
     */
    protected function getActionServiceId($name, $adminName)
    {
        $mapping = LAGAdminBundle::getDefaultActionServiceMapping();
        
        if (!key_exists($name, $mapping)) {
            throw new LogicException('Action "'.$name.'" service id was not found for admin "'.$adminName.'"');
        }
        
        return $mapping[$name];
    }
}
