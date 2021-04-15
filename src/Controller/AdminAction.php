<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Factory\AdminFactoryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\View\RedirectView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class AdminAction
{
    private ParametersExtractorInterface $extractor;
    private AdminFactoryInterface $adminFactory;
    private Environment $twig;

    public function __construct(ParametersExtractorInterface $extractor, AdminFactoryInterface $adminFactory, Environment $twig)
    {
        $this->extractor = $extractor;
        $this->adminFactory = $adminFactory;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $adminName = $this->extractor->getAdminName($request);
        $admin = $this->adminFactory->create($adminName);
        $admin->handleRequest($request);
        $view = $admin->createView();

        if ($view instanceof RedirectView) {
            return new RedirectResponse($view->getUrl());
        }

        return new Response($this->twig->render($view->getTemplate(), [
            'admin' => $view,
        ]));
    }
}
