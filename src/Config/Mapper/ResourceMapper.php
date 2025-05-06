<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config\Mapper;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use LAG\AdminBundle\Config\Source\RootSnakeCaseSource;
use LAG\AdminBundle\Config\Transformer\SnakeCaseTransformer;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Metadata\Resource;

final readonly class ResourceMapper
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

    public function fromArray(array $data): Resource
    {
        $properties = array_map(
            fn (array $data): PropertyInterface => new PropertyMapper()->fromArray($data),
            $data['properties'] ?? [],
        );
        $operations = array_map(
            fn (array $data): OperationInterface => new OperationMapper()->fromArray($data),
            $data['operations'] ?? []
        );

        unset($data['properties']);
        unset($data['operations']);

        /** @var Resource $resource */
        $resource = $this->builder
            ->mapper()
            ->map(Resource::class, RootSnakeCaseSource::array($data))
        ;

        return $resource
            ->withOperations($operations)
            ->withProperties($properties)
        ;
    }

    public function toArray(Resource $resource): array
    {
        return $this->builder
            ->registerTransformer(new SnakeCaseTransformer())
            ->registerTransformer(function (Resource $object, callable $next): mixed {
                $result = $next();

                $result['operations'] = array_map(
                    fn (OperationInterface $operation): array => new OperationMapper()->toArray($operation),
                    $object->getOperations(),
                );
                $result['properties'] = array_map(
                    fn (PropertyInterface $property): array => new PropertyMapper()->toArray($property),
                    $object->getProperties(),
                );

                return $result;
            })
            ->normalizer(Format::array())
            ->normalize($resource)
        ;
    }
}
