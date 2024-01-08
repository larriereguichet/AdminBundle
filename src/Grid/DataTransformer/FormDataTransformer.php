<?php

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Metadata\Property\Form;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Component\Form\FormFactoryInterface;

class FormDataTransformer implements PropertyDataTransformerInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
    )
    {
    }

    public function supports(PropertyInterface $property, mixed $data): bool
    {
        return $property instanceof Form;
    }

    public function transform(PropertyInterface $property, mixed $data): mixed
    {
        assert($property instanceof Form);
        $form = $this->formFactory->create($property->getFormType(), $data, $property->getFormOptions());

        return $form->createView();
    }
}
