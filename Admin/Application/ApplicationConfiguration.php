<?php

namespace BlueBear\AdminBundle\Admin\Application;

use Symfony\Component\HttpFoundation\Request;

class ApplicationConfiguration
{
    public $title;

    public $lang;

    public $layout;

    public $blockTemplate;

    public function hydrateFromConfiguration(array $applicationConfiguration, Request $request)
    {
        $this->lang = $request->getLocale();
        $this->layout = $applicationConfiguration['layout'];
        $this->blockTemplate = $applicationConfiguration['block_template'];

        if (array_key_exists('title', $applicationConfiguration)) {
            $this->title = $applicationConfiguration['title'];
        }
    }
}