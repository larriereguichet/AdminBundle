<?php

namespace LAG\AdminBundle\Action\Render;

use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Twig\Environment;

class ActionRenderer implements ActionRendererInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private Environment $environment,
    ) {
    }

    public function render(Action $action, mixed $data = null): string
    {
        if ($action->getRouteName()) {
            $url = $this->urlGenerator->generateFromRouteName($action->getRouteName(), $action->getRouteParameters(), $data);
        } else {
            $url = $this->urlGenerator->generateFromOperationName($action->getResourceName(), $action->getOperationName(), $data);
        }
        return $this->environment->render($action->getTemplate(), [
            'action' => $action,
            'url' => $url,
        ]);
    }
}
