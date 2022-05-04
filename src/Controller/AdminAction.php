<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Admin\Factory\AdminFactoryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\View\Handler\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAction
{
    public function __construct(
        private ParametersExtractorInterface $parametersExtractor,
        private AdminFactoryInterface $adminFactory,
        private ViewHandlerInterface $viewHandler,
    )
    {
    }

    public function __invoke(Request $request): Response
    {
        $adminName = $this->parametersExtractor->getAdminName($request);
        $admin = $this->adminFactory->create($adminName);
        $admin->handleRequest($request);
        $view = $admin->createView();

        return $this->viewHandler->handle($view);
    }
}
