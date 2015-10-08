<?php

namespace BlueBear\AdminBundle\Tests;

use BlueBear\AdminBundle\Admin\Filter;

class FilterFactoryFunctionalTest extends Base
{
    public function testCreate()
    {
        $this->initApplication();
        $filterFactory = $this->container->get('bluebear.admin.filter_factory');
        $filtersConfiguration = $this->getFakeFiltersConfiguration();

        foreach ($filtersConfiguration as $fieldName => $filterConfiguration) {
            $action = $filterFactory->create($fieldName, $filterConfiguration);
            $this->doTestActionForConfiguration($action, $filterConfiguration, $fieldName);
        }
    }

    protected function getFakeFiltersConfiguration()
    {
        return [
            'id' => [
                'type' => 'select'
            ],
        ];
    }

    protected function doTestActionForConfiguration(Filter $filter, array $configuration, $fieldName)
    {
        $this->assertEquals($fieldName, $filter->getFieldName());

        if (array_key_exists('type', $configuration)) {
            // test configured title
            $this->assertEquals($configuration['type'], $filter->getType());
        } else {
            // test default title
            $this->assertEquals(Filter::TYPE_SELECT, $filter->getType());
        }
    }
}
