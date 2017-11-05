<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\Configuration\LinkConfiguration;
use LAG\AdminBundle\Field\EntityAwareInterface;
use LAG\AdminBundle\Field\TwigAwareInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Link extends StringField implements EntityAwareInterface, TwigAwareInterface
{
    /**
     * @var Object
     */
    protected $entity;

    /**
     * Render link template filled with configured options.
     *
     * @param mixed $value
     * @return string
     */
    public function render($value)
    {
        $text = $this->options['text'] ?: parent::render($value);
        $parameters = [];
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->options['parameters'] as $parameterName => $fieldName) {
            if (!$fieldName) {
                $fieldName = $parameterName;
            }
            $parameters[$parameterName] = $accessor->getValue($this->entity, $fieldName);
        }

        $render = $this->twig->render($this->options['template'], [
            'text' => $text,
            'parameters' => $parameters,
            'options' => $this->options,
        ]);

        return $render;
    }

    /**
     * Define field type.
     *
     * @return string
     */
    public function getType()
    {
        return 'link';
    }

    /**
     * Define entity. It will be use to fill parameters with properties values.
     *
     * @param $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
    
    /**
     * Return the Field's configuration class.
     *
     * @return string
     */
    public function getConfigurationClass()
    {
        return LinkConfiguration::class;
    }
}
