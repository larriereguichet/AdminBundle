<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Metadata\Application;

interface ApplicationContextInterface
{
    /**
     * Return the current application object according to request parameters.
     */
    public function getApplication(): Application;

    /**
     * Return true if the current request has an application.
     */
    public function hasApplication(): bool;
}
