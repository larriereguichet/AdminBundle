<?php

namespace LAG\AdminBundle\Event\Listener\Request;

use LAG\AdminBundle\Admin\Configuration\ActionConfigurationMapper;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Event\Events\RequestEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\ActionFactoryInterface;

/**
 * Define the current action according to the routing configuration.
 */
class ActionListener
{
    private ActionFactoryInterface $actionFactory;
    private AdminHelperInterface $helper;

    public function __construct(ActionFactoryInterface $actionFactory, AdminHelperInterface $helper)
    {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
    }

    public function __invoke(RequestEvent $event): void
    {
        $actionName = $event->getRequest()->get('_action');

        if ($actionName === null) {
            throw new Exception('The "_action" parameter was not found in the request. The route should wrongly configured');
        }
        // Define the current admin for the helper to ease other service to get this admin
        $admin = $event->getAdmin();
        $this->helper->setAdmin($admin);

        $mapper = new ActionConfigurationMapper();

        // Create the current action according to the configuration
        $action = $this->actionFactory->create($actionName, $mapper->map($actionName, $admin->getConfiguration()));
        $event->setAction($action);
    }
}
