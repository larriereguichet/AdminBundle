<?php

namespace LAG\AdminBundle\Action;

use Exception;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Behaviors\AdminAwareTrait;
use LAG\AdminBundle\DataProvider\Loader\EntityLoaderInterface;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Responder\ResponderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class Action implements ActionInterface
{
    /**
     * Action name.
     *
     * @var string
     */
    protected $name;
    
    /**
     * Action configuration (it has been resolved so it should be valid).
     *
     * @var ActionConfiguration
     */
    protected $configuration;
    
    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    protected $formFactory;
    
    /**
     * @var FieldInterface[]
     */
    protected $fields = [];

    /**
     * @var FormInterface[]
     */
    protected $forms = [];

    /**
     * @var EntityLoaderInterface
     */
    private $entityLoader;

    /**
     * Action constructor.
     *
     * @param string                $name
     * @param ActionConfiguration   $configuration
     * @param EntityLoaderInterface $entityLoader
     * @param array                 $fields
     * @param array                 $forms
     *
     * @throws Exception
     */
    public function __construct(
        $name,
        ActionConfiguration $configuration,
        EntityLoaderInterface $entityLoader,
        array $fields = [],
        array $forms = []
    ) {
        $this->name = $name;
        $this->configuration = $configuration;
        $this->fields = $fields;
        $this->forms = $forms;
        $this->entityLoader = $entityLoader;

        if (!$configuration->isResolved()) {
            throw new Exception('The configuration should be resolved before being used in an action');
        }
    }

    /**
     * @inheritdoc
     *
     * @param ActionConfiguration $configuration
     */
    public function setConfiguration(ActionConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }
    
    /**
     * @inheritdoc
     *
     * @return ActionConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
    
    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function isLoadingRequired()
    {
        return Admin::LOAD_STRATEGY_NONE !== $this
            ->configuration
            ->getParameter('load_strategy')
        ;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Return the loaded form.
     *
     * @return FormInterface[]
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * Return true if all the submitted form in the request are valid.
     *
     * @return bool
     */
    public function isValid()
    {
        foreach ($this->forms as $form) {

            if (!$form->isSubmitted()) {
                continue;
            }

            if (!$form->isValid()) {
                return false;
            }
        }

        return true;
    }
}
