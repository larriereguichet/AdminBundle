<?php

declare(strict_types=1);

namespace LAG\AdminBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LAGAdminBundle extends Bundle
{
    // Request Admin parameters
    // TODO from configuration
    public const REQUEST_PARAMETER_ADMIN = '_admin';
    public const REQUEST_PARAMETER_ACTION = '_action';

    public function build(ContainerBuilder $container)
    {
    }

    public function getPath(): string
    {
        return __DIR__.'/../';
    }
}
