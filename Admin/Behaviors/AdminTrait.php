<?php

namespace LAG\AdminBundle\Admin\Behaviors;

use Pagerfanta\Pagerfanta;

trait AdminTrait
{
    use EntityLabelTrait;
    use TranslationKeyTrait;

    /**
     * @var Pagerfanta
     */
    protected $pager;

    /**
     * Return the current unique entity.
     *
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
    public function getUniqueEntityLabel()
    {
        $entity = $this->getUniqueEntity();
        $label = $this->getEntityLabel($entity);

        return $label;
    }
}
