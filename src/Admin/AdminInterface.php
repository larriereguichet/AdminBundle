<?php

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Admin\Resource\AdminResource;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

interface AdminInterface
{
    /**
     * Do not load entities on handleRequest (for create method for example).
     */
    const LOAD_STRATEGY_NONE = 'strategy_none';

    /**
     * Load one entity on handleRequest (edit method for example).
     */
    const LOAD_STRATEGY_UNIQUE = 'strategy_unique';

    /**
     * Load multiple entities on handleRequest (list method for example).
     */
    const LOAD_STRATEGY_MULTIPLE = 'strategy_multiple';

    /**
     * Handle the request: load the forms and the entities.
     *
     * @throws Exception
     */
    public function handleRequest(Request $request);

    /**
     * Return the request handled by the admin. If no request was previously handled, an exception will be thrown. The
     * getRequest() method should be after the handleRequest() method.
     *
     * @throws Exception
     */
    public function getRequest(): Request;

    /**
     * Return the Admin name.
     */
    public function getName(): string;

    /**
     * Return the class of the entity managed by the Admin.
     */
    public function getEntityClass(): string;

    /**
     * Return the Resource used to create the Admin.
     */
    public function getResource(): AdminResource;

    /**
     * Return the event dispatcher associated to the Admin.
     */
    public function getEventDispatcher(): EventDispatcherInterface;

    /**
     * Return the Admin configuration.
     */
    public function getConfiguration(): AdminConfiguration;

    /**
     * Return the current Action if defined. If it is not defined, an exception will be thrown.
     */
    public function getAction(): ActionInterface;

    /**
     * Return true if the current action is defined.
     */
    public function hasAction(): bool;

    /**
     * Return the data loaded into the Admin.
     */
    public function getData();

    /**
     * Return the forms associated to the Admin.
     *
     * @return FormInterface[]
     */
    public function getForms(): array;

    /**
     * Return the form associated to the given name. An exception will be thrown if the form does not exists.
     *
     * @throws Exception
     */
    public function getForm(string $name): FormInterface;

    /**
     * Return true if a form with the given name exists.
     */
    public function hasForm(string $name): bool;

    /**
     * Create a new view for the template.
     */
    public function createView(): ViewInterface;
}
