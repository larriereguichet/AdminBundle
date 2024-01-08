<?php

namespace LAG\AdminBundle\View\Component;

use LAG\AdminBundle\Metadata\Property\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin_form',
    template: '@LAGAdmin/components/form.html.twig',
)]
class FormComponent
{
    public FormView $formView;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    public function mount(Form $property, mixed $data): void
    {
        $form = $this->formFactory->create($property->getFormType(), $data, $property->getFormOptions());
        $this->formView = $form->createView();
    }
}
