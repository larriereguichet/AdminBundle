<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use Closure;
use Doctrine\Common\Collections\Collection;
use Iterator;
use LAG\AdminBundle\Field\View\TextView;
use LAG\AdminBundle\Field\View\View;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArrayField extends AbstractField
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'glue' => ', ',
                'sortable' => false,
            ])
            ->setAllowedTypes('glue', 'string')
        ;
    }

    public function createView(): View
    {
        return new TextView($this->getName(), $this->getOptions(), $this->getDataTransformer());
    }

    public function getDataTransformer(): ?Closure
    {
        return function ($data) {
            if ($data === null) {
                return '';
            }

            if ($data instanceof Collection) {
                $data = $data->toArray();
            }

            if ($data instanceof Iterator) {
                $data = iterator_to_array($data);
            }

            return implode($this->getOption('glue'), $data);
        };
    }
}
