<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\AbstractField;
use LAG\AdminBundle\Field\Configuration\ActionCollectionConfiguration;
use LAG\AdminBundle\Field\EntityAwareInterface;
use LAG\AdminBundle\Field\TwigAwareInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig_Environment;

class ActionCollection extends AbstractField implements TwigAwareInterface, EntityAwareInterface
{
    /**
     * @var Twig_Environment
     */
    protected $twig;
    
    /**
     * @var mixed
     */
    protected $entity;
    
    /**
     * Render value of the field.
     *
     * @param mixed $value Value to render
     *
     * @return mixed
     */
    public function render($value)
    {
        $actions = $this->options['actions'];
        $normalizedActions = [];
        
        foreach ($actions as $action => $options) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $normalizedParameters = [];
    
            foreach ($options['parameters'] as $parameter => $parameterOptions) {
                $normalizedParameters[$parameter] = $accessor->getValue($this->entity, $parameter);
            }
            $options['parameters'] = $normalizedParameters;
            $normalizedActions[$action] = $options;
        }
        
        return $this
            ->twig
            ->render('@LAGAdmin/Field/actionCollection.html.twig', [
                'actions' => $normalizedActions,
            ])
        ;
    }
    
    /**
     * Return field type.
     *
     * @return string
     */
    public function getType()
    {
        return AbstractField::TYPE_ACTION_COLLECTION;
    }
    
    /**
     * Defines entity for field.
     *
     * @param $entity
     *
     * @return void
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
    
    /**
     * Define twig environment.
     *
     * @param Twig_Environment $twig
     *
     * @return void
     */
    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
    
    /**
     * Return the Field's configuration class.
     *
     * @return string
     */
    public function getConfigurationClass()
    {
        return ActionCollectionConfiguration::class;
    }
}
