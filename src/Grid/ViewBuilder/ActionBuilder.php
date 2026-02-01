<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Metadata\Attribute\Action;
use LAG\AdminBundle\Grid\View\Cell;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ActionBuilder implements ActionBuilderInterface
{
    public function __construct(
        private ResourceUrlGeneratorInterface $urlGenerator,
        private ConditionMatcherInterface $conditionMatcher,
        private TranslatorInterface $translator,
        private AttributeBuilderInterface $attributeBuilder,
    ) {
    }

    public function buildAction(Action $action, mixed $data, array $context = []): ?Cell
    {
        $actionAttributes = $action->getAttributes();

        if (!$this->conditionMatcher->matchCondition($action, $data, $context)) {
            return null;
        }

        if ($action->getTitle() !== null) {
            $actionAttributes['title'] = $this->translator->trans($action->getTitle(), [], $action->getTranslationDomain());
        }

        return new Cell(
            name: $action->getName(),
            attributes: $this->attributeBuilder->buildAttributes($actionAttributes),
            property: $action,
            template: $action->getTemplate(),
            label: $action->getLabel(),
            data: $this->urlGenerator->generateFromUrl($action, $data),
        );
    }
}
