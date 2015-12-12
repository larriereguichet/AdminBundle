<?php

namespace LAG\AdminBundle\Admin\Behaviors;

use Symfony\Component\PropertyAccess\PropertyAccess;

trait EntityLabelTrait
{
    public function getEntityLabel($entity)
    {
        $label = '';
        $accessor = PropertyAccess::createPropertyAccessor();

        if ($accessor->isReadable($entity, 'label')) {
            $label = $entity->getLabel();
        } else if ($accessor->isReadable($entity, 'title')) {
            $label = $entity->getTitle();
        } else if ($accessor->isReadable($entity, 'name')) {
            $label = $entity->getName();
        } else if ($accessor->isReadable($entity, '__toString')) {
            $label = $entity->__toString();
        } else if ($accessor->isReadable($entity, 'content')) {
            $label = strip_tags(substr($entity->getContent(), 0, 100));
        } else if ($accessor->isReadable($entity, 'id')) {
            $label = $entity->getId();
        }
        return $label;
    }
}
