<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config\Mapper;

use CuyZ\Valinor\Mapper\Object\DynamicConstructor;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use LAG\AdminBundle\Config\Source\RootSnakeCaseSource;
use LAG\AdminBundle\Config\Transformer\InheritedPropertyTransformer;
use LAG\AdminBundle\Config\Transformer\SnakeCaseTransformer;
use LAG\AdminBundle\Metadata\Collection;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Metadata\Text;

final class PropertyMapper
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

    public function fromArray(mixed $data): PropertyInterface
    {
        $entryProperty = null;

//        if (!empty($data['entry_property'])) {
//            $entryProperty = $this->fromArray($data['entry_property']);
//            dump($entryProperty);
//        }

        //$data['entry_property'] = new Text();
//        unset($data['entry_property']);

        $property = $this->builder
            //->infer(PropertyInterface::class, fn (string $__class__): string => $__class__)
            ->registerConstructor(
                #[DynamicConstructor]
                function (string $className, array $value): Collection {
                    if (isset($value['entryProperty'])) {
                        $value['entryProperty'] = $this->fromArray($value['entryProperty']);
                    }
                    unset($value['__class__']);

                    return new $className(...$value);
                })

            ->mapper()
            ->map($data['__class__'], RootSnakeCaseSource::array($data))
        ;

        if ($property instanceof Collection && $entryProperty !== null) {
            $property = $property->withEntryProperty($entryProperty);
        }

        return $property;
    }

    public function toArray(PropertyInterface $property): array
    {
        $data = $this->builder
            ->registerTransformer(new SnakeCaseTransformer())
            ->registerTransformer(new InheritedPropertyTransformer())
            ->normalizer(Format::array())
            ->normalize($property)
        ;
        $data['__class__'] = get_class($property);

        if ($property instanceof Collection && $property->getEntryProperty() !== null) {
            $data['entry_property'] = $this->toArray($property->getEntryProperty());
            $data['entry_property']['__class__'] = get_class($property->getEntryProperty());
        }

        return $data;
    }
}
