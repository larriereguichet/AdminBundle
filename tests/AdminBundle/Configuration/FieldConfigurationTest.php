<?php

namespace LAG\AdminBundle\Tests\Configuration;

use LAG\AdminBundle\Configuration\FieldConfiguration;
use LAG\AdminBundle\Tests\TestCase;

class FieldConfigurationTest extends TestCase
{
    /**
     * @dataProvider configureDataProvider
     */
    public function testConfigure(array $configuration, array $expectedConfiguration): void
    {
        $fieldConfiguration = new FieldConfiguration();
        $fieldConfiguration->configure($configuration);

        $this->assertEquals($expectedConfiguration, $fieldConfiguration->toArray());
        $this->assertEquals($expectedConfiguration['type'], $fieldConfiguration->getType());
        $this->assertEquals($expectedConfiguration['options'], $fieldConfiguration->getOptions());
    }

    public function configureDataProvider(): array
    {
        return [
            [
                [
                    'type' => null,
                ],
                [
                    'type' => 'auto',
                    'options' => [],
                ],
            ],
            [
                [
                    'type' => null,
                    'options' => null,
                ],
                [
                    'type' => 'auto',
                    'options' => [],
                ],
            ],
        ];
    }
}
