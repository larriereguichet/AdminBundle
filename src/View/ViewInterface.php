<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;

/**
 * A view represents admin data that needs to be passed in the Twig template.
 */
interface ViewInterface
{
    /**
     * Return the Twig template associated to the view.
     */
    public function getTemplate(): string;

    /**
     * Return the base template the main template (returned buy getTemplate()) should extends.
     */
    public function getBase(): string;

    public function getActionConfiguration(): ActionConfiguration;

    public function getName(): string;

    public function getActionName(): string;

    public function getData();

    public function getFields(): array;

    public function getAdminConfiguration(): AdminConfiguration;

    public function getForms(): array;
}
