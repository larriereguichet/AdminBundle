<?php

namespace BlueBear\AdminBundle\Admin\Render;

use Symfony\Component\OptionsResolver\OptionsResolver;

class StringRenderer implements RendererInterface
{
    protected $length;
    protected $replace;

    public function __construct(array $configuration = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'length' => null,
            'replace' => '...'
        ]);
        $configuration = $resolver->resolve($configuration);

        $this->length = $configuration['length'];
        $this->replace = $configuration['replace'];
    }

    public function render($value)
    {
        if ($this->length) {
            $value = substr($value, $this->length) . $this->replace;
        }
        return $value;
    }
}
