<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config\Mapper;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use LAG\AdminBundle\Config\Source\RootSnakeCaseSource;
use LAG\AdminBundle\Config\Transformer\InheritedPropertyTransformer;
use LAG\AdminBundle\Config\Transformer\SnakeCaseTransformer;
use LAG\AdminBundle\Metadata\FilterInterface;

final class FilterMapper
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

    public function toArray(FilterInterface $filter): array
    {
        $data = $this->builder
            ->registerTransformer(new SnakeCaseTransformer())
            ->registerTransformer(new InheritedPropertyTransformer())
            ->normalizer(Format::array())
            ->normalize($filter)
        ;
        $data['class'] = get_class($filter);

        return $data;
    }

    public function fromArray(array $data): FilterInterface
    {
        return $this->builder
            ->mapper()
            ->map($data['class'] ?? FilterInterface::class, RootSnakeCaseSource::array($data))
        ;
    }
}
