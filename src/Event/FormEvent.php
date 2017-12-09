<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

class FormEvent extends Event
{
    /**
     * @var FormInterface[]
     */
    private $forms = [];

    /**
     * @param FormInterface $form
     *
     * @throws Exception
     */
    public function addForm(FormInterface $form)
    {
        if (array_key_exists($form->getName(), $this->forms)) {
            throw new Exception('A form with the name "'.$form->getName().'" was already added');
        }

        $this->forms[$form->getName()] = $form;
    }

    /**
     * @return FormInterface[]
     */
    public function getForms()
    {
        return $this->forms;
    }
}
