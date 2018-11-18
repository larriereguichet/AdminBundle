<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\AdminResource;
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
     *
     * @throws Exception
     */
    public function handleRequest(Request $request);

    /**
     * Return the Admin name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Return the Resource used to create the Admin.
     *
     * @return AdminResource
     */
    public function getResource(): AdminResource;

    /**
     * Return the event dispatcher associated to the Admin.
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface;

    /**
     * Return the Admin configuration.
     *
     * @return AdminConfiguration
     */
    public function getConfiguration(): AdminConfiguration;

    /**
     * Return the current Action if defined. If it is not defined, an exception will be thrown.
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
     * Return the form associated to the given name. An exception will be thrown if the form does not exists.
     *
     * @param string $name
     *
     * @return FormInterface
     *
     * @throws Exception
     */
    public function getForm(string $name): FormInterface;

    /**
     * Return true if a form with the given name exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasForm(string $name): bool;

    /**
     * Create a new view for the template.
     *
     * @return ViewInterface
     */
    public function createView(): ViewInterface;
}
