<?php

namespace LAG\AdminBundle\Assets\Registry;

use LAG\AdminBundle\Exception\Exception;

/**
 * Manage script to avoid adding javascript into the body, and allow dumping them into the head and footer section of
 * the html page.
 */
interface ScriptRegistryInterface
{
    /**
     * Sometimes js script should registered in the head.
     */
    const LOCATION_HEAD = 'head';

    /**
     * Usually where the js belongs.
     */
    const LOCATION_FOOTER = 'footer';

    /**
     * Register an array for a location. A custom template and context can be provided.
     *
     * @param string      $location Where the script will be dumped (only head and footer are allowed)
     * @param string      $script   The script name. If $template is provided, the script name is only used as array key
     * @param string|null $template The template name (MyBundle:Some:template.html.twig)
     * @param array       $context  The context given to the Twig template
     */
    public function register(string $location, string $script, string $template = null, array $context = []): void;

    /**
     * Remove the registered script at the given location.
     */
    public function unregister(string $location, string $script): void;

    /**
     * Dump the scripts for the given location.
     *
     * @param string $location usually "footer" or "head"
     */
    public function dump(string $location): string;

    /**
     * Check if the location is correct (only head and footer are allowed).
     *
     * @throws Exception
     */
    public function hasLocation(string $location): bool;

    /**
     * Return true if the given script is registered at the given location, false otherwise.
     */
    public function hasScript(string $location, string $script): bool;
}
