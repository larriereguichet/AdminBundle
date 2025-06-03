<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handle response creation.
 */
interface ResponseHandlerInterface extends ContentResponseHandlerInterface, RedirectResponseHandlerInterface
{
}
