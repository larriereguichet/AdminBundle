<?php

namespace BlueBear\AdminBundle\Admin\Render;

use BlueBear\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StringRenderer implements RendererInterface
{
    protected $length;
    protected $replace;

    public function __construct(array $configuration = [], ApplicationConfiguration $application)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'length' => $application->getStringLength(),
            'replace' => $application->getStringLengthTruncate()
        ]);
        $configuration = $resolver->resolve($configuration);

        $this->length = $configuration['length'];
        $this->replace = $configuration['replace'];
    }

    public function render($value)
    {
        if ($this->length) {
            $value = substr($value, 0, $this->length) . $this->replace;
        }
        return $value;
    }
}
