<?php

namespace BlueBear\AdminBundle\Admin\Render;

use BlueBear\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Traversable;

class ArrayRenderer implements RendererInterface
{
    protected $glue;

    public function __construct(array $options = [], ApplicationConfiguration $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'glue' => ', '
        ]);
        $options = $resolver->resolve($options);
        $this->glue = $options['glue'];
    }

    public function render($value)
    {
        if (!is_array($value) || $value instanceof Traversable) {
            throw new Exception('Value should be an array instead of ' . gettype($value));
        }
        return implode($this->glue, $value);
    }
}
