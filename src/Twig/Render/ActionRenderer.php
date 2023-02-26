<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Render;

use LAG\AdminBundle\Exception\Validation\InvalidActionException;
use LAG\AdminBundle\Metadata\Link;
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
        Link $action,
        mixed $data = null,
        array $options = []
    ): string {
        $errors = $this->validator->validate($action, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidActionException($errors);
        }

        if ($action->getRoute()) {
            $url = $this->urlGenerator->generateFromRouteName(
                $action->getRoute(),
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
