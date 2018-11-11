<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Event\AbstractEvent;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\Form\FormInterface;

class FormEvent extends AbstractEvent
{
    /**
     * @var FormInterface[]
     */
    private $forms = [];

    /**
     * @param FormInterface $form
     * @param string        $identifier
     *
     * @throws Exception
     */
    public function addForm(FormInterface $form, string $identifier)
    {
        if (array_key_exists($identifier, $this->forms)) {
            throw new Exception('A form with the identifier "'.$identifier.'" was already added');
        }
        $this->forms[$identifier] = $form;
    }

    /**
     * @return FormInterface[]
     */
    public function getForms()
    {
        return $this->forms;
    }
}
