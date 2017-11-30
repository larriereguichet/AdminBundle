<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\AbstractField;
use LAG\AdminBundle\Field\Configuration\CollectionConfiguration;
use LAG\AdminBundle\Field\EntityAwareInterface;
use LAG\AdminBundle\Field\FieldInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig_Environment;

class Collection extends AbstractField implements EntityAwareInterface
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var Object
     */
    protected $entity;

    /**
     * @param mixed $value
     * @return string
     */
    public function render($value)
    {
        $render = '';
        $accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->getPropertyAccessor();

        /** @var FieldInterface $field */
        foreach ($this->options['fields'] as $field) {
            $value = null;
            // if name starts with a underscore, it is a custom field, not mapped to the entity
            if (substr($field->getName(), 0, 1) != '_') {
                // get raw value from object
                $value = $accessor->getValue($this->entity, $field->getName());
            }
            // if the field required an entity to be rendered, we set it
            if ($field instanceof EntityAwareInterface) {
                $field->setEntity($this->entity);
            }
            $render .= $field->render($value);
        }

        return $render;
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('fields')
            ->setAllowedTypes('fields', 'array');
    }

    public function getType()
    {
        return 'collection';
    }

    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

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
        return CollectionConfiguration::class;
    }
}
