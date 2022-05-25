<?php

namespace LAG\AdminBundle\EventListener\Request;

use LAG\AdminBundle\Admin\Context\AdminContextInterface;
use LAG\AdminBundle\Admin\Factory\AdminFactoryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class AdminListener
{
    public function __construct(
        private ParametersExtractorInterface $extractor,
        private AdminContextInterface $context,
        private AdminFactoryInterface $adminFactory,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->extractor->supports($request)) {
            return;
        }
        $adminName = $this->extractor->getAdminName($request);
        $actionName = $this->extractor->getActionName($request);

        if ($this->context->hasAdmin()) {
            return;
        }
        $admin = $this->adminFactory->create($adminName, []);
        $admin->setCurrentAction($actionName);

        $this->context->setAdmin($admin);

    }
}
