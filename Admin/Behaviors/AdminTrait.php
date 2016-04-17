<?php

namespace LAG\AdminBundle\Admin\Behaviors;

use Pagerfanta\Pagerfanta;
use Symfony\Component\PropertyAccess\PropertyAccess;

trait AdminTrait
{
    /**
     * @var Pagerfanta
     */
    protected $pager;

    /**
     * @return object
     */
    public abstract function getUniqueEntity();

    /**
     * @return Pagerfanta
     */
    public function getPager()
    {
        return $this->pager;
    }

    /**
     * Try to find a property to get a label from an entity. If found, it returns the property value through the
     * property accessor.
     *
     * @return string
     */
    public function getEntityLabel()
    {
        $label = '';
        $accessor = PropertyAccess::createPropertyAccessor();
        $entity = $this->getUniqueEntity();

        if ($accessor->isReadable($entity, 'label')) {
            $label = $accessor->getValue($entity, 'label');
        } else if ($accessor->isReadable($entity, 'title')) {
            $label = $accessor->getValue($entity, 'title');
        } else if ($accessor->isReadable($entity, 'name')) {
            $label = $accessor->getValue($entity, 'name');
        } else if ($accessor->isReadable($entity, '__toString')) {
            $label = $accessor->getValue($entity, '__toString');
        } else if ($accessor->isReadable($entity, 'content')) {
            $label = strip_tags(substr($label = $accessor->getValue($entity, 'content'), 0, 100));
        } else if ($accessor->isReadable($entity, 'id')) {
            $label = $accessor->getValue($entity, 'id');
        }
        return $label;
    }
}
