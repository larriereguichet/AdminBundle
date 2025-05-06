<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config\Mapper;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use LAG\AdminBundle\Config\Source\RootSnakeCaseSource;
use LAG\AdminBundle\Config\Transformer\InheritedPropertyTransformer;
use LAG\AdminBundle\Config\Transformer\SnakeCaseTransformer;
use LAG\AdminBundle\Metadata\Grid;

use function Symfony\Component\String\u;

final readonly class GridMapper
{
    private MapperBuilder $builder;

    public function __construct()
    {
        $this->builder = new MapperBuilder()
            ->enableFlexibleCasting()
            ->allowSuperfluousKeys()
            ->allowPermissiveTypes()
        ;
    }

    public function fromArray(array $data): Grid
    {
        foreach ($data['actions'] ?? [] as $index => $action) {
            foreach ($action as $name => $value) {
                $data['actions'][$index][u($name)->camel()->toString()] = $value;
            }
        }

        foreach ($data['collection_actions'] ?? [] as $index => $action) {
            foreach ($action as $name => $value) {
                $data['collection_actions'][$index][u($name)->camel()->toString()] = $value;
            }
        }

        return $this->builder
            ->mapper()
            ->map(Grid::class, RootSnakeCaseSource::array($data))
        ;
    }

    public function toArray(Grid $grid): array
    {
        return $this->builder
            ->registerTransformer(new SnakeCaseTransformer())
            ->registerTransformer(new InheritedPropertyTransformer())
            ->normalizer(Format::array())
            ->normalize($grid)
        ;
    }
}
