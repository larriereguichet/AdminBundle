<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\Traits\EntityAwareTrait;
use LAG\AdminBundle\Field\Traits\RendererAwareTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionField extends AbstractField implements EntityAwareFieldInterface, RendererAwareFieldInterface
{
    use EntityAwareTrait;
    use RendererAwareTrait;

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

        foreach ($this->options['fields'] as $field) {
            $render .= $this->renderer->render($field, $this->entity);
        }

        return $render;
    }
}
