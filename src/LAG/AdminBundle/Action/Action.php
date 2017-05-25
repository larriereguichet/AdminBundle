<?php

namespace LAG\AdminBundle\Action;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Behaviors\AdminAwareTrait;
use Symfony\Component\Form\FormFactoryInterface;

abstract class Action implements ActionInterface
{
    use AdminAwareTrait;
    
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
            ->getParameter('load_method')
        ;
    }
    
    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
