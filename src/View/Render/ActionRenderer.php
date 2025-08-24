<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Render;

use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Environment;
use Twig\Runtime\EscaperRuntime;

// TODO remove
final readonly class ActionRenderer implements ActionRendererInterface
{
    public function __construct(
        private ResourceUrlGeneratorInterface $urlGenerator,
        private Environment $environment,
        private TranslatorInterface $translator,
    ) {
    }

    public function renderAction(Action $action, mixed $data = null): string
    {
        if ($action->getTitle() !== null && $action->getAttribute('title') === null) {
            $title = $this->translator->trans($action->getTitle(), [], $action->getTranslationDomain());
            /** @var Action $action */
            $action = $action->withAttribute('title', $title);
        }

        return $this->environment->render($action->getTemplate(), [
            'data' => $this->urlGenerator->generateFromUrl($action, $data),
            'options' => $action,
            'attributes' => new ComponentAttributes($action->getAttributes(), $this->environment->getRuntime(EscaperRuntime::class)),
        ]);
    }
}
