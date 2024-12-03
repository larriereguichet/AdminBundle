<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Grid\Render\LinkRendererInterface;
use LAG\AdminBundle\Resource\Metadata\Action;
use LAG\AdminBundle\Resource\Metadata\Link;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class RenderHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private Environment $environment,
        private UrlGeneratorInterface $urlGenerator,
        private LinkRendererInterface $linkRenderer,
        private TranslatorInterface $translator,
    ) {
    }

    public function generateLinkUrl(Link $link, mixed $data = null): string
    {
        return $this->urlGenerator->generateUrl($link, $data);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function renderLink(Link $link, mixed $data = null, array $options = []): string
    {
        return $this->linkRenderer->render($link, $data, $options);
    }

    public function generateUrl(
        string $resource,
        string $operation,
        mixed $data = null,
        ?string $applicationName = null,
    ): string {
        return $this->urlGenerator->generateFromOperationName(
            $resource,
            $operation,
            $data,
            $applicationName,
        );
    }

    public function renderAction(Action $action, mixed $data): string
    {
        if ($action->getTitle() !== null && $action->getAttribute('title') === null) {
            $title = $this->translator->trans($action->getTitle(), [], $action->getTranslationDomain());
            /** @var Action $action */
            $action = $action->withAttribute('title', $title);
        }

        return $this->environment->render($action->getTemplate(), [
            'data' => $this->urlGenerator->generateUrl($action, $data),
            'options' => $action,
            'attributes' => new ComponentAttributes($action->getAttributes()),
        ]);
    }
}
