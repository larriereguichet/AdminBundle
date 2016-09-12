<?php

namespace LAG\AdminBundle\Form\Factory;

use Exception;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FormFactory
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * FormFactory constructor.
     *
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param $formType
     * @param $entity
     * @param AdminInterface $admin
     *
     * @return FormInterface
     *
     * @throws Exception
     */
    public function create($formType, $entity, AdminInterface $admin)
    {
        // an valid entity should be passed
        if (!is_object($entity)) {
            throw new Exception('Invalid entity for form creation');
        }

        if (null === $formType) {
            $form = $this->guessForm($admin, $entity);
        } else {
            // a form type is defined, we use the form factory
            $form = $this
                ->formFactory
                ->create($formType, $entity);
        }

        return $form;
    }

    /**
     * Use Symfony's standard guesser to create the form type from the fields
     *
     * @param AdminInterface $admin
     * @param $entity
     * @return FormInterface
     */
    protected function guessForm(AdminInterface $admin, $entity)
    {
        $action = $admin->getCurrentAction();
        $form = $this
            ->formFactory
            ->createNamed($admin->getName(), $entity);

        foreach ($action->getFields() as $field) {
            $form->add($field->getName());
        }

        return $form;
    }
}
