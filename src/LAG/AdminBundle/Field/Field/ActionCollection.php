<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\AbstractField;
use LAG\AdminBundle\Field\EntityAwareInterface;
use LAG\AdminBundle\Field\TwigAwareInterface;
use LogicException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
        $actions = $this
            ->options
            ->get('actions')
        ;
        $normalizedActions = [];
        
        foreach ($actions as $action => $options) {
            $this->normalizeRoute($options, $action);
            $normalizedActions[$action] = $options;
        }
        
        
        return $this
            ->twig
            ->render('@LAGAdmin/Field/actionCollection.html.twig', [
                'actions' => $normalizedActions,
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('actions')
            ->setAllowedTypes('actions', 'array')
            ->setNormalizer('actions', function (Options $options, $actions) {
    
                $normalizedActions = [];
                
                foreach ($actions as $action => $options) {
                    $this->normalizeIcon($options, $action);
                    $this->normalizeClass($options, $action);
                    $this->normalizeText($options, $action);
                    
                    $normalizedActions[$action] = $options;
                }
    
                return $normalizedActions;
            })
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
    
    protected function normalizeIcon(array &$options, $action)
    {
        $iconMapping = [
            'delete' => 'fa fa-times',
            'edit' => 'fa fa-pencil',
        ];
    
        if (!array_key_exists('icon', $options)) {
            $options['icon'] = '';
        
            if (array_key_exists($action, $iconMapping)) {
                $options['icon'] = $iconMapping[$action];
            }
        }
    }
    
    protected function normalizeClass(array &$options, $action)
    {
        $classMapping = [
            'delete' => 'btn btn-danger',
            'edit' => 'btn btn-default',
        ];
    
        if (!array_key_exists('class', $options)) {
            $options['class'] = '';
        
            if (array_key_exists($action, $classMapping)) {
                $options['class'] = $classMapping[$action];
            }
        }
        $normalizedActions[$action] = $options;
    }
    
    protected function normalizeRoute(array &$options, $action)
    {
        if (!array_key_exists('route', $options)) {
            throw new LogicException(
                'You should provide a route for the Action "'.$action.'" in ActionCollection Field'
            );
        }
    
        if (array_key_exists('parameters', $options)) {
    
            if (!is_array($options['parameters'])) {
                throw new LogicException(
                    'You should provide an array of parameters for route configuration in action "'.$action.'"'
                );
            }
            $accessor = PropertyAccess::createPropertyAccessor();
            $normalizedParameters = [];
            
            foreach ($options['parameters'] as $parameter => $parameterOptions) {
                $normalizedParameters[$parameter] = $accessor->getValue($this->entity, $parameter);
            }
            $options['parameters'] = $normalizedParameters;
        }
    }
    
    protected function normalizeText(array &$options, $action)
    {
        if (!array_key_exists('text', $options)) {
            $options['text'] = ucfirst($action);
        }
    }
}
