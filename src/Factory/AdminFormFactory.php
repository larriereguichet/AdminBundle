<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\DataProvider\Registry\DataProviderRegistryInterface;
use LAG\AdminBundle\Utils\FormUtils;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminFormFactory implements AdminFormFactoryInterface
{
    private DataProviderRegistryInterface $registry;
    private FormFactoryInterface $formFactory;
    private FieldFactoryInterface $fieldFactory;

    public function __construct(
        DataProviderRegistryInterface $registry,
        FormFactoryInterface $formFactory,
        FieldFactoryInterface $fieldFactory
    ) {
        $this->registry = $registry;
        $this->formFactory = $formFactory;
        $this->fieldFactory = $fieldFactory;
    }

    public function createEntityForm(AdminInterface $admin, Request $request, object $data = null): FormInterface
    {
        if ($data === null) {
            $dataProviderName = $admin->getConfiguration()->getDataProvider();
            $data = $this->registry->get($dataProviderName)->create($admin->getEntityClass());
        }
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

            if ($name === 'id') {
                continue;
            }
            $formType = FormUtils::convertShortFormType($definition->getType());
            $formOptions = array_merge(
                FormUtils::getFormTypeOptions($definition->getType()),
                $definition->getFormOptions()
            );

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
