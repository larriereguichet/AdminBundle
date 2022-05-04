<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Request;

use LAG\AdminBundle\Action\Factory\ActionFactoryInterface;
use LAG\AdminBundle\Admin\Helper\AdminContextInterface;
use LAG\AdminBundle\Event\Events\RequestEvent;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;

/**
 * Define the current action according to the routing configuration.
 */
class ActionListener
{
    private ActionFactoryInterface $actionFactory;
    private AdminContextInterface $helper;
    private ParametersExtractorInterface $extractor;

    public function __construct(
        ActionFactoryInterface       $actionFactory,
        AdminContextInterface        $helper,
        ParametersExtractorInterface $extractor
    ) {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
        $this->extractor = $extractor;
    }

    public function __invoke(RequestEvent $event): void
    {
        $actionName = $this->extractor->getActionName($event->getRequest());
        // Define the current admin for the helper to ease other service to get this admin
        $admin = $event->getAdmin();
        $this->helper->setAdmin($admin);

        // Create the current action according to the configuration
        $action = $this->actionFactory->create($actionName, $admin->getConfiguration()->getAction($actionName));
        $event->setAction($action);
    }
}
