<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Factory\Configuration;

use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Exception\ConfigurationException;

interface AdminConfigurationFactoryInterface
{
    /**
     * Create a new admin configuration object, resolved with the given options. If the configuration is invalid, an
     * exception will be thrown.
     *
     * @throws ConfigurationException
     */
    public function create(string $adminName, array $options = []): AdminConfiguration;
}
