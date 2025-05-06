<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config\Mapper;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use LAG\AdminBundle\Config\Source\RootSnakeCaseSource;
use LAG\AdminBundle\Config\Transformer\InheritedPropertyTransformer;
use LAG\AdminBundle\Config\Transformer\SnakeCaseTransformer;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\FilterInterface;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Metadata\Update;

final class OperationMapper
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

    public function fromArray(mixed $data): OperationInterface
    {
        $filters = array_map(fn (array $data): FilterInterface => new FilterMapper()->fromArray($data), $data['filters'] ?? []);
        unset($data['filters']);

        $operation =  $this->builder
            ->mapper()
            ->map($data['class'] ?? OperationInterface::class, RootSnakeCaseSource::array($data))
        ;

        if ($operation instanceof CollectionOperationInterface) {
            $operation = $operation->withFilters($filters);
        }

        return $operation;
    }

    public function toArray(OperationInterface $operation): array
    {
        $data = $this->builder
            ->registerTransformer(new SnakeCaseTransformer())
            ->registerTransformer(new InheritedPropertyTransformer())
            ->registerTransformer(function (OperationInterface $object, callable $next): mixed {
                $result = $next();

                if ($object instanceof CollectionOperationInterface) {
                    $result['filters'] = array_map(
                        fn (FilterInterface $filter): array => new FilterMapper()->toArray($filter),
                        $object->getFilters(),
                    );
                }

                return $result;
            })
            ->normalizer(Format::array())
            ->normalize($operation)
        ;
        $data['class'] = get_class($operation);

        return $data;
    }
}
