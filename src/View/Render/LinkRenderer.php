<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Render;

use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Routing\UrlGenerator\LinkUrlGeneratorInterface;
use Twig\Environment;

final readonly class LinkRenderer implements LinkRendererInterface
{
    public function __construct(
        private LinkUrlGeneratorInterface $urlGenerator,
        private Environment $environment,
    ) {
    }

    public function render(
        Link $link,
        mixed $data = null,
        array $options = [],
    ): string {
        $url = $this->urlGenerator->generateUrl($link, $data);

        return $this->environment->render($link->getTemplate(), [
            'link' => $link->withUrl($url),
            'options' => $options,
        ]);
    }
}
