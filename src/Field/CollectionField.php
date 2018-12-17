<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\Traits\EntityAwareTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CollectionField extends AbstractField implements EntityAwareFieldInterface
{
    use EntityAwareTrait;

    public function isSortable(): bool
    {
        return false;
    }

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        $resolver
            ->setRequired('fields')
            ->setAllowedTypes('fields', 'array')
        ;
    }

    public function render($value = null): string
    {
        $render = '';
        $accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->getPropertyAccessor();

        /** @var FieldInterface $field */
        foreach ($this->options['fields'] as $field) {
            $value = null;
            // if name starts with a underscore, it is a custom field, not mapped to the entity
            if ('_' != substr($field->getName(), 0, 1)) {
                // get raw value from object
                $value = $accessor->getValue($this->entity, $field->getName());
            }
            // if the field required an entity to be rendered, we set it
            if ($field instanceof EntityAwareFieldInterface) {
                $field->setEntity($this->entity);
            }
            $render .= $field->render($value);
        }

        return $render;
    }
}
