<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Metadata\Attribute\Action;
use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\View\Render\ActionRendererInterface;
use LAG\AdminBundle\View\Render\LinkRendererInterface;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class RenderHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private LinkRendererInterface $linkRenderer,
        private ActionRendererInterface $actionRenderer,
    ) {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function renderLink(Link $link, mixed $data = null, array $options = []): string
    {
        return $this->linkRenderer->render($link, $data, $options);
    }

    // TODO remove
    public function renderAction(Action $action, mixed $data = null): string
    {
        return $this->actionRenderer->renderAction($action, $data);
    }
}
