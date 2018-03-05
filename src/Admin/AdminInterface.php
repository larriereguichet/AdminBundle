<?php

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Resource\Resource;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

interface AdminInterface
{
    public function handleRequest(Request $request);

    public function getName(): string;

    public function getResource(): Resource;

    public function getEventDispatcher(): EventDispatcherInterface;

    public function getConfiguration(): AdminConfiguration;

    public function getAction(): ActionInterface;

    public function getEntities();

    public function createView(): ViewInterface;
}
