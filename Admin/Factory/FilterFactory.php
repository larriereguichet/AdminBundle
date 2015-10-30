<?php

namespace LAG\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Admin\Filter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterFactory
{
    /**
     * @param string $fieldName
     * @param array  $filterConfiguration
     *
     * @return Filter
     */
    public function create($fieldName, array $filterConfiguration)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'type' => Filter::TYPE_SELECT,
        ]);
        // setting filters default configuration
        $resolver->setAllowedValues('type', [
            Filter::TYPE_SELECT,
        ]);
        // adding filters to the action
        $filterConfiguration = $resolver->resolve($filterConfiguration);
        $filter = new Filter();
        $filter->setFieldName($fieldName);
        $filter->setType($filterConfiguration['type']);

        return $filter;
    }
}
