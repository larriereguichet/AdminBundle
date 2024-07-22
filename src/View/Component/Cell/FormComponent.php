<?php

namespace LAG\AdminBundle\View\Component\Cell;

use LAG\AdminBundle\Grid\View\CellView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:form',
    template: '@LAGAdmin/components/cells/form.html.twig',
)]
final class FormComponent
{
    public FormView $form;
    public mixed $data;
    public ?string $template = null;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    public function mount(
        mixed $data,
        CellView $cell,
    ): void {
        /** @var \LAG\AdminBundle\Resource\Metadata\Form $property */
        $property = $cell->property;
        $form = $this->formFactory->create($property->getForm(), $data, $property->getFormOptions());
        $this->form = $form->createView();
        $this->template = $property->getFormTemplate();
        $this->data = $data;
    }
}
