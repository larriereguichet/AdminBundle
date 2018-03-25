<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Resource\Resource;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface AdminInterface
{
    /**
     * Handle the request: load the forms and the entities.
     *
     * @param Request $request
     */
    public function handleRequest(Request $request);

    /**
     * Return the Admin's name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Return the Resource use for creating the Admin.
     *
     * @return Resource
     */
    public function getResource(): Resource;

    /**
     * Return the event dispatcher associated to the Admin.
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface;

    /**
     * Return the Admin's configuration.
     *
     * @return AdminConfiguration
     */
    public function getConfiguration(): AdminConfiguration;

    /**
     * Return the current action if defined.
     *
     * @return ActionInterface
     */
    public function getAction(): ActionInterface;

    /**
     * Return true if the current action is defined.
     *
     * @return bool
     */
    public function hasAction(): bool;

    /**
     * Return the loaded entities in the Admin.
     *
     * @return Collection
     */
    public function getEntities();

    /**
     * Return the forms associated to the Admin.
     *
     * @return FormInterface[]
     */
    public function getForms(): array;

    /**
     * Create a new view for the template.
     *
     * @return ViewInterface
     */
    public function createView(): ViewInterface;
}
