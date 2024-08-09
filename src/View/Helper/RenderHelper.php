<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Condition\ConditionMatcherInterface;
use LAG\AdminBundle\Resource\Metadata\Action;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Environment;

final readonly class RenderHelper implements RenderHelperInterface
{
    public function __construct(
        private Environment $environment,
        private UrlGeneratorInterface $urlGenerator,
        private ConditionMatcherInterface $conditionMatcher,
        private TranslatorInterface $translator,
    ) {
    }

    public function renderAction(Action $action, mixed $data): string
    {
        if (
            $action->getCondition() !== null
            && !$this->conditionMatcher->matchCondition($data, $action->getCondition(), [], $action->getWorkflow())
        ) {
            return '';
        }

        if ($action->getTitle() !== null && $action->getAttribute('title') === null) {
            $title = $this->translator->trans($action->getTitle(), [], $action->getTranslationDomain());
            $action = $action->withAttribute('title', $title);
        }

        return $this->environment->render($action->getTemplate(), [
            'data' => $this->urlGenerator->generateUrl($action, $data),
            'options' => $action,
            'attributes' => new ComponentAttributes($action->getAttributes()),
        ]);
    }
}
