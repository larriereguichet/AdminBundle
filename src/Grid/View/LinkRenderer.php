<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\Validation\InvalidActionException;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

class LinkRenderer implements LinkRendererInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private ValidatorInterface $validator,
        private Environment $environment,
    ) {
    }

    public function render(
        Link $link,
        mixed $data = null,
        array $options = []
    ): string {
        $errors = $this->validator->validate($link, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidActionException($errors);
        }

        if ($link->getRoute()) {
            $url = $this->urlGenerator->generateFromRouteName(
                $link->getRoute(),
                $link->getRouteParameters(),
                $data,
            );
        } elseif ($link->getResourceName() && $link->getOperationName()) {
            $url = $this->urlGenerator->generateFromOperationName(
                $link->getResourceName(),
                $link->getOperationName(),
                $data,
            );
        } elseif ($link->getUrl()) {
            $url = $link->getUrl();
        } else {
            throw new Exception('Unable to generate a route for the given link');
        }

        return $this->environment->render($link->getTemplate(), [
            'link' => $link->withUrl($url),
            'options' => $options,
        ]);
    }
}
