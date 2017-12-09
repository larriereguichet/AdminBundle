<?php

namespace LAG\AdminBundle\Action;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminAwareInterface;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Responder\ResponderInterface;
use Symfony\Component\Form\FormInterface;

interface ActionInterfaceOLD
{
    /**
     * Return the Action's configuration.
     *
     * @return ActionConfiguration
     */
    public function getConfiguration();
    
    /**
     * Return true if the Action require entity loading.
     *
     * @return bool
     */
    public function isLoadingRequired();
    
    /**
     * The Action's name.
     *
     * @return string
     */
    public function getName();
    
    /**
     * @return FieldInterface[]
     */
    public function getFields();

    /**
     * Return the array of form for the current action.
     *
     * @return FormInterface[]
     */
    public function getForms();

    /**
     * Return true if all the submitted form in the request are valid.
     *
     * @return bool
     */
    public function isValid();
}
