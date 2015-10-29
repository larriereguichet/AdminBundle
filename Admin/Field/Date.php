<?php

namespace LAG\AdminBundle\Admin\Field;

use LAG\AdminBundle\Admin\Field;
use DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Date field
 */
class Date extends Field
{
    /**
     * Date format
     *
     * @var string
     */
    protected $format;

    /**
     * Render and format a Datetime
     *
     * @param $value
     * @return string
     */
    public function render($value)
    {
        if ($value instanceof DateTime) {
            $value = $value->format($this->format);
        }
        return $value;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'format' => $this->configuration->getDateFormat()
        ]);
    }

    public function setOptions(array $options)
    {
        $this->format = $options['format'];
    }

    public function getType()
    {
        return 'date';
    }
}
