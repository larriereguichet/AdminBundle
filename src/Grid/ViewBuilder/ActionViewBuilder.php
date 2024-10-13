<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
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
            template: $action->getTemplate(),
            label: $action->getLabel(),
            options: $action,
            data: $this->urlGenerator->generateUrl($action, $data),
            attributes: $actionAttributes,
        );
    }
}
