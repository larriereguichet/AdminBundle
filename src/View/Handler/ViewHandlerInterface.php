<?php

namespace LAG\AdminBundle\View\Handler;

use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\HttpFoundation\Response;

interface ViewHandlerInterface
{
    /**
     * Create a Symfony response from an admin view or a redirect view.
     */
    public function handle(ViewInterface $view): Response;
}
