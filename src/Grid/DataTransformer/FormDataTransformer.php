<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\DataTransformer;

use LAG\AdminBundle\Exception\UnexpectedTypeException;
use LAG\AdminBundle\Metadata\Form;
use LAG\AdminBundle\Metadata\PropertyInterface;
use Symfony\Component\Form\FormFactoryInterface;

final readonly class FormDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function transform(PropertyInterface $property, mixed $data): \Symfony\Component\Form\FormView
    {
        if (!$property instanceof Form) {
            throw new UnexpectedTypeException($property, Form::class);
        }
        $form = $this->formFactory->create($property->getForm(), $data, $property->getFormOptions());

        return $form->createView();
    }
}
