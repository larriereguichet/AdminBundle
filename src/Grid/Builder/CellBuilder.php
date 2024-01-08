<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Builder;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\ValidationException;
use LAG\AdminBundle\Grid\DataTransformer\PropertyDataTransformerInterface;
use LAG\AdminBundle\Grid\View\Cell;
use LAG\AdminBundle\Metadata\Property\CollectionPropertyInterface;
use LAG\AdminBundle\Metadata\Property\CompositePropertyInterface;
use LAG\AdminBundle\Metadata\Property\ConfigurableProperty;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class CellBuilder implements CellBuilderInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private PropertyDataTransformerInterface $dataTransformer,
    ) {
    }

    public function build(PropertyInterface $property, mixed $data, ?FormView $form = null, array $attributes = []): Cell
    {
        if ($property instanceof ConfigurableProperty) {
            $property->configure($data);
        }

        if ($property->getPropertyPath() !== null && $property->getPropertyPath() !== '.') {
            $accessor = PropertyAccess::createPropertyAccessor();

            if (!$accessor->isReadable($data, $property->getPropertyPath())) {
                throw new Exception(sprintf('The property path "%s" is not readable', $property->getPropertyPath()));
            }
            $data = $accessor->getValue($data, $property->getPropertyPath());
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

        if ($property instanceof CompositePropertyInterface) {
            $children = [];

            foreach ($property->getChildren() as $child) {
                $childCell = $this->build($child, $data, $form, $attributes);
                $children[$childCell->name] = $childCell;
            }
        }

        if ($property instanceof CollectionPropertyInterface) {
            $children = [];

            foreach ($data as $item) {
                $childCell = $this->build($property->getPropertyType(), $item, $form, $attributes);
                $children[$childCell->name] = $childCell;
            }
        }

        return new Cell(
            name: $property->getName(),
            data: $data,
            template: $property->getTemplate(),
            attributes: $attributes,
            property: $property,
            form: $form,
            children: $children ?? [],
        );
    }
}
