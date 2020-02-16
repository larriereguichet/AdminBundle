<?php

namespace LAG\AdminBundle\Tests\Configuration;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionConfigurationTest extends AdminTestBase
{
    public function testResolveOptions()
    {
        $adminConfiguration = $this->createAdminConfigurationMock([
            [
                'actions', [
                    'list' => [],
                    'custom' => [],
                ],
            ],
            [
                'routing_name_pattern', 'routing.{action}.{admin}',
            ],
            [
                'translation_pattern', 'translation.{action}.{admin}',
            ],
            [
                'string_length', 100,
            ],
            [
                'string_length_truncate', '...',
            ],
        ]);

        $configuration = new ActionConfiguration('custom', 'my_admin', $adminConfiguration);
        $resolver = new OptionsResolver();

        $configuration->configureOptions($resolver);

        $resolver->resolve();
    }

    /**
     * @expectedException Exception
     */
    public function testResolveOptionsWithMissingAction()
    {
        $adminConfiguration = $this->createAdminConfigurationMock([
            ['actions', [],],
        ]);

        $configuration = new ActionConfiguration('my_action', 'my_admin', $adminConfiguration);
        $resolver = new OptionsResolver();

        $configuration->configureOptions($resolver);

        $resolver->resolve();
    }
}
