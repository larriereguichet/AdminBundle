<?php

namespace LAG\AdminBundle\Action\Render;

use LAG\AdminBundle\Exception\Validation\InvalidActionException;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

class ActionRenderer implements ActionRendererInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private ValidatorInterface $validator,
        private Environment $environment,
    ) {
    }

    public function render(
        Action $action,
        mixed $data = null,
        array $options = []
    ): string {
        $errors = $this->validator->validate($action, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidActionException($errors);
        }

        if ($action->getRouteName()) {
            $url = $this->urlGenerator->generateFromRouteName(
                $action->getRouteName(),
                $action->getRouteParameters(),
                $data,
            );
        } else {
            $url = $this->urlGenerator->generateFromOperationName(
                $action->getResourceName(),
                $action->getOperationName(),
                $data,
            );
        }

        return $this->environment->render($action->getTemplate(), [
            'action' => $action,
            'url' => $url,
            'options' => $options,
        ]);
    }
}
