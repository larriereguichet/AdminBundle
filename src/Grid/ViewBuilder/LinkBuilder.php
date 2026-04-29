<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Routing\UrlGenerator\LinkUrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class LinkBuilder implements LinkBuilderInterface
{
    public function __construct(
        private LinkUrlGeneratorInterface $urlGenerator,
        private ConditionMatcherInterface $conditionMatcher,
        private TranslatorInterface $translator,
        private AttributeBuilderInterface $attributeBuilder,
    ) {
    }

    public function buildLink(Link $link, mixed $data, array $context = []): ?CellView
    {
        $actionAttributes = $link->getAttributes();

        if (!$this->conditionMatcher->matchCondition($link, $data, $context)) {
            return null;
        }

        if (empty($actionAttributes['title'])) {
            $actionAttributes['title'] = $this->translator->trans($link->getText(), [], $context['translation_domain'] ?? 'admin');
        }

        return new CellView(
            name: $link->getName(),
            attributes: $this->attributeBuilder->buildAttributes($actionAttributes),
            property: $link,
            template: $link->getTemplate(),
            label: $link->getLabel(),
            data: $this->urlGenerator->generateUrl($link, $data),
        );
    }
}
