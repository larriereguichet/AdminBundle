<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Environment;
use Twig\Runtime\EscaperRuntime;

final readonly class HeaderViewBuilder implements HeaderViewBuilderInterface
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    public function buildHeader(
        OperationInterface $operation,
        Grid $grid,
        PropertyInterface $property,
        array $context = []
    ): HeaderView {
        if ($property->getLabel() === false) {
            return new HeaderView(
                name: $property->getName(),
                attributes: new ComponentAttributes([], $this->environment->getRuntime(EscaperRuntime::class))
            );
        }

        return new HeaderView(
            name: $property->getName(),
            attributes: new ComponentAttributes($property->getHeaderAttributes(), $this->environment->getRuntime(EscaperRuntime::class)),
            template: $grid->getHeaderTemplate(),
            label: $property->getLabel() ?: '',
            translationDomain: $grid->getTranslationDomain(),
            sort: $context['sort'] ?? null,
            order: $context['order'] ?? null,
            sortable: $grid->isSortable() && $property->isSortable(),
        );
    }
}
