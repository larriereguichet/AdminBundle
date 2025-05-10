<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Render;

use LAG\AdminBundle\Exception\InvalidLinkException;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

final readonly class LinkRenderer implements LinkRendererInterface
{
    public function __construct(
        private ResourceUrlGeneratorInterface $urlGenerator,
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
            throw new InvalidLinkException($errors);
        }
        $url = $this->urlGenerator->generateFromUrl($link, $data);

        return $this->environment->render($link->getTemplate(), [
            'link' => $link->withUrl($url),
            'options' => $options,
        ]);
    }
}
