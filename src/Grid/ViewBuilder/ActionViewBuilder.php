<?php

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Condition\ConditionMatcherInterface;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Resource\Metadata\Action;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ActionViewBuilder implements ActionViewBuilderInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private ConditionMatcherInterface $conditionMatcher,
        private TranslatorInterface $translator,
    ) {
    }

    public function buildActions(Action $action, mixed $data, array $attributes = []): ?CellView
    {
        $actionAttributes = $action->getAttributes();

        if (
            $action->getCondition() !== null
            && !$this->conditionMatcher->matchCondition($data, $action->getCondition(), [], $action->getWorkflow())
        ) {
            return null;
        }

        if ($action->getTitle() !== null) {
            $actionAttributes['title'] = $this->translator->trans($action->getTitle(), [], $action->getTranslationDomain());
        }

        return new CellView(
            name: $action->getName(),
            label: $action->getLabel(),
            options: $action,
            template: $action->getTemplate(),
            data: $this->urlGenerator->generateUrl($action, $data),
            attributes: $actionAttributes,
        );
    }
}