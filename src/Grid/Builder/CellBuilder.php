<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Builder;

use LAG\AdminBundle\Exception\ValidationException;
use LAG\AdminBundle\Grid\DataTransformer\PropertyDataTransformerInterface;
use LAG\AdminBundle\Grid\View\Cell;
use LAG\AdminBundle\Metadata\Property\CollectionPropertyInterface;
use LAG\AdminBundle\Metadata\Property\CompositePropertyInterface;
use LAG\AdminBundle\Metadata\Property\ConfigurableProperty;
use LAG\AdminBundle\Metadata\Property\Form;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use LAG\AdminBundle\Resource\DataMapper\ResourceDataMapper;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class CellBuilder implements CellBuilderInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private PropertyDataTransformerInterface $dataTransformer,
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function build(
        PropertyInterface $property,
        mixed $data,
        array $attributes = [],
    ): Cell {
        $data = ResourceDataMapper::mapData($data, $property->getPropertyPath());
        $form = null;

        if ($property instanceof Form) {
            $form = $this->formFactory->create($property->getForm(), $data, $property->getFormOptions());
        }

        if ($this->dataTransformer->supports($property, $data)) {
            $data = $this->dataTransformer->transform($property, $data);
        }

        if ($property->getAllowedDataType() !== null) {
            $errors = $this->validator->validate($data, [new Type(type: $property->getAllowedDataType())]);

            if ($errors->count() > 0) {
                throw new ValidationException('Data passed to the "'.$property->getName().'" cell are not valid :', $errors);
            }
        }
        $children = [];


        if ($property instanceof CollectionPropertyInterface) {
            foreach ($data as $item) {
                $childCell = $this->build($property->getPropertyType(), $item, $attributes);
                $children[$childCell->name] = $childCell;
            }
        }
        $attributes = array_merge($property->getAttributes(), $attributes);

        return new Cell(
            name: $property->getName(),
            data: $data,
            template: $property->getTemplate(),
            component: $property->getComponent(),
            attributes: $attributes,
            property: $property,
            form: $form?->createView(),
            children: $children,
        );
    }
}
