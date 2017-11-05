<?php

namespace LAG\AdminBundle\Field\Configuration;

use JK\Sam\Configuration\Configuration;
use LogicException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionCollectionConfiguration extends Configuration
{
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
        }
    }
    
    protected function normalizeText(array &$options, $action)
    {
        if (!array_key_exists('text', $options)) {
            $options['text'] = ucfirst($action);
        }
    }
}
