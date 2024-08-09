<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Resource\Metadata\Operation;
use LAG\AdminBundle\View\Render\CellRendererInterface;
use LAG\AdminBundle\View\Render\GridRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GridExtension extends AbstractExtension
{
    public function __construct(
        private readonly GridRendererInterface $gridRenderer,
        private readonly CellRendererInterface $cellRenderer,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_grid', [$this, 'renderGrid'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_grid_header', [$this, 'renderHeader'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_cell', [$this, 'renderCell'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_merge_attributes', [$this, 'mergeAttributes']),
        ];
    }

    public function renderGrid(GridView $grid, Operation $operation): string
    {
        return $this->gridRenderer->render($grid, $operation, []);
    }

    public function renderHeader(HeaderView $header): string
    {
        // TODO
    }

    public function renderCell(CellView $cell, array $options = []): string
    {
        return $this->cellRenderer->render($cell, $options);
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
