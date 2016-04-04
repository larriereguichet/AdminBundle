<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\Field;
use DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Date field.
 */
class Date extends Field
{
    /**
     * Date format.
     *
     * @var string
     */
    protected $format;

    /**
     * Render and format a Datetime.
     *
     * @param $value
     *
     * @return string
     */
    public function render($value)
    {
        if ($value instanceof DateTime) {
            $value = $value->format($this->format);
        }

        return $value;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'format' => $this
                ->configuration
                ->getParameter('date_format'),
        ]);
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->format = $options['format'];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'date';
    }
}
