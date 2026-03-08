<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Runtime;

use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\View\Render\LinkRendererInterface;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class RenderRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private LinkRendererInterface $linkRenderer,
    ) {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function renderLink(Link $link, mixed $data = null, array $options = []): string
    {
        return $this->linkRenderer->render($link, $data, $options);
    }
}
