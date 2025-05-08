<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ActionViewBuilder implements ActionViewBuilderInterface
{
    public function __construct(
        private ResourceUrlGeneratorInterface $urlGenerator,
        private ConditionMatcherInterface $conditionMatcher,
        private TranslatorInterface $translator,
    ) {
    }

    public function buildActions(Action $action, mixed $data, array $context = []): ?CellView
    {
        $actionAttributes = $action->getAttributes();

        if (!$this->conditionMatcher->matchCondition($action, $data, $context)) {
            return null;
        }

        if ($action->getTitle() !== null) {
            $actionAttributes['title'] = $this->translator->trans($action->getTitle(), [], $action->getTranslationDomain());
        }

        return new CellView(
            name: $action->getName(),
            options: $action,
            template: $action->getTemplate(),
            label: $action->getLabel(),
            data: $this->urlGenerator->generateFromUrl($action, $data),
            attributes: $actionAttributes,
        );
    }
}
