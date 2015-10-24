<?php

namespace BlueBear\AdminBundle\Admin\Field;

use BlueBear\AdminBundle\Admin\Field;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig_Environment;

class Collection extends Field implements EntityFieldInterface
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var Field[]
     */
    protected $fields = [];

    protected $entity;

    public function render($value)
    {
        $render = '';

        foreach ($this->fields as $field) {
            // if the field required an entity to be rendered, we set it
            if ($field instanceof EntityFieldInterface) {
                $field->setEntity($this->entity);
            }
            $render .= $field->render($value);
        }
        return $render;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('fields')
            ->setAllowedTypes('fields', 'array');
    }

    /**
     * Set options values after options resolving
     *
     * @param array $options
     * @return mixed
     */
    public function setOptions(array $options)
    {
        $this->fields = $options['fields'];
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
}
