<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoField extends AbstractField
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'template' => '@LAGAdmin/fields/auto.html.twig',
                'date_format' => 'd/m/Y',
            ])
            ->setAllowedTypes('date_format', 'string')
        ;
    }

    public function getDataTransformer(): ?\Closure
    {
        return function ($data) {
            if ($data === null) {
                return '';
            }

            if (\is_string($data)) {
                return $data;
            }

            if (is_numeric($data)) {
                return (string) $data;
            }

            if (\is_array($data)) {
                return implode(',', $data);
            }

            if ($data instanceof \DateTimeInterface) {
                return $data->format($this->getOption('date_format'));
            }

            if ($data instanceof Collection) {
                return implode(',', $data->toArray());
            }

            if (\is_object($data) && method_exists($data, '__toString')) {
                return (string) $data;
            }

            return '';
        };
    }
}
