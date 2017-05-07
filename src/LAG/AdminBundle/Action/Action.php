<?php

namespace LAG\AdminBundle\Action;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Responder\CreateResponder;
use LAG\AdminBundle\Action\Responder\DeleteResponder;
use LAG\AdminBundle\Action\Responder\EditResponder;
use LAG\AdminBundle\Action\Responder\ResponderInterface;
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
     * @var ResponderInterface|CreateResponder|DeleteResponder|EditResponder
     */
    protected $responder;
    
    /**
     * Action constructor.
     *
     * @param string               $name
     * @param FormFactoryInterface $formFactory
     * @param ResponderInterface   $responder
     */
    public function __construct(
        $name,
        FormFactoryInterface $formFactory,
        ResponderInterface $responder
    ) {
        $this->name = $name;
        $this->formFactory = $formFactory;
        $this->responder = $responder;
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
