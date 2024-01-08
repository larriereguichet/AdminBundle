<?php

namespace LAG\AdminBundle\Metadata\Grid;

use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class CardGrid extends Grid
{
    public function __construct(
        #[Assert\GreaterThan(value: 0, message: 'The card grid should have at least one column')]
        private int $columns = 3,

        #[Assert\Choice(choices: ['columns', 'group'], message: 'The available card grid layouts are "columns" and "group"')]
        private string $layout = 'columns',

        private ?string $imageProperty = null,

        ?string $name = null,
        ?string $translationDomain = null,
        array $attributes = [],
        array $rowAttributes = [],
        array $headerRowAttributes = [],
        array $headerAttributes = [],
    ) {
        parent::__construct(
            name: $name,
            translationDomain: $translationDomain,
            attributes: $attributes,
            rowAttributes: $rowAttributes,
            headerRowAttributes: $headerRowAttributes,
            headerAttributes: $headerAttributes,
        );
    }

    public function getColumns(): int
    {
        return $this->columns;
    }

    public function getImageProperty(): ?string
    {
        return $this->imageProperty;
    }

    public function getTemplate(): string
    {
        return '@LAGAdmin/grids/card/card_grid.html.twig';
    }

    public function getLayout(): string
    {
        return $this->layout;
    }
}
