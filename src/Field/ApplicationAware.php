<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;

trait ApplicationAware
{
    private ApplicationConfiguration $applicationConfiguration;

    public function getApplicationConfiguration(): ApplicationConfiguration
    {
        return $this->applicationConfiguration;
    }

    public function setApplicationConfiguration(ApplicationConfiguration $applicationConfiguration): void
    {
        $this->applicationConfiguration = $applicationConfiguration;
    }
}
