<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\View\Helper\GridHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class GridExtension extends AbstractExtension
{
    public function __construct(
        private readonly GridHelperInterface $helper,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_grid', $this->helper->renderGrid(...), ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_header', $this->helper->renderHeader(...), ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_cell', $this->helper->renderCell(...), ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_merge_attributes', $this->mergeAttributes(...)),
        ];
    }

    public function mergeAttributes(array $attributes = [], array $required = [], array $defaults = []): array
    {
        $mergedAttributes = $required;
        $attributes = $defaults + $attributes;

        foreach ($attributes as $key => $attribute) {
            if (!\array_key_exists($key, $mergedAttributes)) {
                $mergedAttributes[$key] = $attribute;
            } else {
                $mergedAttributes[$key] .= ' '.$attribute;
            }
        }

        return $mergedAttributes;
    }
}
