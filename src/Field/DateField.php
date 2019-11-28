<?php

namespace LAG\AdminBundle\Field;

use DateTime;
use Exception;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateField extends AbstractField
{
    public function isSortable(): bool
    {
        return true;
    }

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        $resolver->setDefaults([
            'format' => $actionConfiguration->getParameter('date_format'),
        ]);
    }

    /**
     * @param mixed $value
     *
     * @throws Exception
     */
    public function render($value = null): string
    {
        if (null === $value) {
            return '';
        }

        if (!$value instanceof DateTime) {
            throw new Exception('Expected Datetime, got '.gettype($value));
        }

        return $value->format($this->options['format']);
    }
}
