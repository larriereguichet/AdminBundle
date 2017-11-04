<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
use LAG\AdminBundle\Admin\Request\RequestHandler;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This class allow an Admin and an ActionConfiguration to be injected into the current Action/controller. It also add
 * the global Admin application configuration to Twig global parameters.
 */
class KernelSubscriber implements EventSubscriberInterface
{
    /**
     * @var RequestHandler
     */
    private $requestHandler;
    
    /**
     * @var AdminFactory
     */
    private $adminFactory;
    
    /**
     * @var ActionFactory
     */
    private $actionFactory;
   
    /**
     * @var ApplicationConfigurationStorage
     */
    private $applicationConfigurationStorage;
    
    /**
     * KernelSubscriber constructor.
     *
     * @param AdminFactory                    $adminFactory
     * @param ActionFactory                   $actionFactory
     * @param RequestHandler                  $requestHandler
     * @param ApplicationConfigurationStorage $applicationConfigurationStorage
     */
    public function __construct(
        AdminFactory $adminFactory,
        ActionFactory $actionFactory,
        RequestHandler $requestHandler,
        ApplicationConfigurationStorage $applicationConfigurationStorage
    ) {
        $this->requestHandler = $requestHandler;
        $this->adminFactory = $adminFactory;
        $this->actionFactory = $actionFactory;
        $this->applicationConfigurationStorage = $applicationConfigurationStorage;
    }
    
    /**
     * Return the subscribed events (kernelController and kernelView).
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
    
    /**
     * On kernelController event, an Admin and an ActionConfiguration can be injected to the current action.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $request = $event->getRequest();
        
        // If the current request is supported by the request handler, we do load the requested Admin and its
        // configurations
        if ($this->requestHandler->supports($request)) {
            // Inject the current Admin into the Controller
            $this
                ->adminFactory
                ->injectAdmin($controller, $request)
            ;
            // Inject the resolved Configuration into the Controller
            $this
                ->actionFactory
                ->injectConfiguration($controller, $request)
            ;
        }
    }
    
    /**
     * On KernelRequest event, we init the Admin factory, so the Admins and Actions configurations will be available
     * in the controller.
     */
    public function onKernelRequest()
    {
        // Init the admin factory
        $this
            ->adminFactory
            ->init()
        ;
    }
}
