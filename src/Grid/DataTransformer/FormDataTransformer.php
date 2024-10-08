<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Resource\Metadata\Form;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use Symfony\Component\Form\FormFactoryInterface;

final readonly class FormDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function transform(PropertyInterface $property, mixed $data): \Symfony\Component\Form\FormView
    {
        \assert($property instanceof Form);
        $form = $this->formFactory->create($property->getForm(), $data, $property->getFormOptions());

        return $form->createView();
    }
}
