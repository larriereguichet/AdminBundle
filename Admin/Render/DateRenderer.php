<?php

namespace BlueBear\AdminBundle\Admin\Render;

use BlueBear\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateRenderer implements RendererInterface
{
    protected $format;

    public function __construct(array $options = [], ApplicationConfiguration $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'format' => $configuration->getDateFormat()
        ]);
        $options = $resolver->resolve($options);
        $this->format = $options['format'];
    }

    /**
     * @param DateTime $value
     */
    public function render($value)
    {
        if ($value instanceof DateTime) {
            $value = $value->format($this->format);
        }
        return $value;
    }

}
