<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Factory\AdminFactory;
use LAG\AdminBundle\View\RedirectView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class AdminAction
{
    /**
     * @var AdminFactory
     */
    private $adminFactory;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * AdminAction constructor.
     *
     * @param AdminFactory      $adminFactory
     * @param Environment $twig
     */
    public function __construct(AdminFactory $adminFactory, Environment $twig)
    {
        $this->adminFactory = $adminFactory;
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function __invoke(Request $request): Response
    {
        $admin = $this->adminFactory->createFromRequest($request);
        $admin->handleRequest($request);
        $view = $admin->createView();

        if ($view instanceof RedirectView) {
            return new RedirectResponse($view->getUrl());
        }

        $content = $this->twig->render($view->getTemplate(), [
            'admin' => $view,
        ]);

        return new Response($content);
    }
}
