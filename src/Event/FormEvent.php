<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormEvent extends Event
{
    /**
     * @var FormInterface[]
     */
    private $forms = [];

    /**
     * @var AdminInterface
     */
    private $admin;

    /**
     * @var Request
     */
    private $request;

    /**
     * AdminEvent constructor.
     *
     * @param AdminInterface   $admin
     * @param Request $request
     */
    public function __construct(AdminInterface $admin, Request $request)
    {
        $this->admin = $admin;
        $this->request = $request;
    }

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

    /**
     * @return AdminInterface
     */
    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
