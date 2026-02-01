<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

/**
 * Create new content or redirect response according to the operation and context.
 */
interface ResponseHandlerInterface extends ContentResponseHandlerInterface, RedirectResponseHandlerInterface
{
}
