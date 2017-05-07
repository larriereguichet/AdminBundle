<?php

namespace LAG\AdminBundle\Admin\Behaviors;

use Symfony\Component\PropertyAccess\PropertyAccess;

trait EntityLabelTrait
{
    /**
     * Try to find a property to get a label from an entity. If found, it returns the property value through the
     * property accessor.
     *
     * @param $entity
     * @return string
     */
    public function getEntityLabel($entity)
    {
        $label = '';
        $accessor = PropertyAccess::createPropertyAccessor();
        $properties = [
            'label',
            'title',
            'name',
            '__toString',
            'content',
            'id',
        ];

        foreach ($properties as $property) {

            if ($accessor->isReadable($entity, $property)) {
                $label = $accessor->getValue($entity, $property);
                break;
            }               
        }
        
        return $label;
    }
}
