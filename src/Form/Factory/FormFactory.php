<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Factory\FieldFactoryInterface;
use LAG\AdminBundle\Utils\FormUtils;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface as SymfonyFormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormFactory implements FormFactoryInterface
{
    public function __construct(
        private SymfonyFormFactoryInterface $formFactory,
        private FieldFactoryInterface $fieldFactory
    ) {
    }

    public function createEntityForm(AdminInterface $admin, Request $request, object $data): FormInterface
    {
        $action = $admin->getAction();
        $formType = $action->getConfiguration()->getForm();

        if ($formType !== null) {
            return $this->formFactory->create($formType, $data, $action->getConfiguration()->getFormOptions());
        }
        $formBuilder = $this->formFactory->createBuilder(FormType::class, $data, [
            'label' => false,
        ]);
        $fieldDefinitions = $this->fieldFactory->createDefinitions(\get_class($data));

        foreach ($fieldDefinitions as $name => $definition) {
            // We do not want to edit those values in a Form
            if (\in_array($name, ['createdAt', 'updatedAt']) && 'datetime' === $definition->getType()) {
                continue;
            }

            if (in_array($name, ['id', 'identifier', 'uuid'])) {
                continue;
            }
            $formType = FormUtils::convertShortFormType($definition->getType());
            $formOptions = $definition->getFormOptions();
            $formBuilder->add($name, $formType, $formOptions);
        }

        return $formBuilder->getForm();
    }

    public function createDeleteForm(AdminInterface $admin, Request $request, $data): FormInterface
    {
        $action = $admin->getAction();
        $actionConfiguration = $action->getConfiguration();

        return $this
            ->formFactory
            ->create($actionConfiguration->getForm(), $data, $actionConfiguration->getFormOptions())
        ;
    }
}
