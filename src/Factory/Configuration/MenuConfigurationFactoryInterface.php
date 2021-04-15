<?php

namespace LAG\AdminBundle\Factory\Configuration;

use LAG\AdminBundle\Configuration\MenuConfiguration;

/**
 * Responsible of creating configuration objects from the array configuration from yaml.
 */
interface MenuConfigurationFactoryInterface
{
    public function create(string $menuName, array $options = []): MenuConfiguration;
}
