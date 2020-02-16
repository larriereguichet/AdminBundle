<?php

namespace LAG\AdminBundle\Assets\Registry;

use LAG\AdminBundle\Exception\Exception;
use Twig\Environment;

/**
 * Manage script to avoid adding javascript into the body, and allow dumping them into the head and footer section of
 * the html page.
 */
class ScriptRegistry implements ScriptRegistryInterface
{
    const DEFAULT_TEMPLATE = '@LAGAdmin/Scripts/scripts.html.twig';

    protected $scripts = [];

    /**
     * @var string
     */
    protected $template;

    /**
     * @var Environment
     */
    protected $environment;

    public function __construct(Environment $environment, string $template = self::DEFAULT_TEMPLATE)
    {
        $this->template = $template;
        $this->environment = $environment;
    }

    public function register(string $location, string $script, string $template = null, array $context = []): void
    {
        $asset = [
            'location' => $location,
            'template' => $template,
            'context' => $context,
        ];

        if (null === $template) {
            // if no template is provided, we use the default script template
            $asset['template'] = $this->template;
            $asset['context']['script'] = $script;
        }
        $this->scripts[$location][$script] = $asset;
    }

    public function unregister(string $location, string $script): void
    {
        if (!$this->hasScript($location, $script)) {
            throw new Exception('The script "'.$script.'" is not registered at the location "'.$location.'". It can be unregistered');
        }
        unset($this->scripts[$location][$script]);
    }

    public function dump(string $location): string
    {
        $content = '';

        foreach ($this->scripts as $location => $scripts) {
            foreach ($scripts as $script) {
                $content .= $this->environment->render($script['template'], $script['context']);
            }
        }

        return $content;
    }

    public function hasLocation(string $location): bool
    {
        return key_exists($location, $this->scripts);
    }

    public function hasScript(string $location, string $script): bool
    {
        return $this->hasLocation($location) && key_exists($script, $this->scripts[$location]);
    }
}
