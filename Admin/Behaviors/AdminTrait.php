<?php

namespace LAG\AdminBundle\Admin\Behaviors;

use Pagerfanta\Pagerfanta;

trait AdminTrait
{
    use EntityLabelTrait {
        getEntityLabel as parentEntityLabel;
    }
    use TranslationKeyTrait;

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
        $entity = $this->getUniqueEntity();
        $label = $this->parentEntityLabel($entity);

        return $label;
    }
}
