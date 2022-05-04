<?php

namespace LAG\AdminBundle\View\Handler;

use LAG\AdminBundle\Admin\View\AdminView;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\View\RedirectView;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class ViewHandler implements ViewHandlerInterface
{
    public function __construct(
        private Environment $environment,
        private RouterInterface $router,
    ) {
    }

    public function handle(ViewInterface $view): Response
    {
        if ($view instanceof AdminView) {
            return new Response($this->environment->render($view->getTemplate(), [
                'admin' => $view,
                'action' => $view->getAction(),
            ]));
        }

        if ($view instanceof RedirectView) {
            return new RedirectResponse($this->router->generate($view->getRoute(), $view->getRouteParameters()));
        }

        throw new Exception(sprintf(
            'The view "%s" is not handle. Decorates the "%s" service to add your view logic',
            get_class($view),
            self::class,
        ));
    }
}
