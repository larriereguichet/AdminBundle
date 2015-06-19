<?php

namespace BlueBear\AdminBundle\Admin\Application;

class ApplicationConfiguration
{
    public $title;

    public $lang;

    public $layout;

    public $blockTemplate;

    public $description;

    public $bootstrap = false;

    public $dateFormat;

    public function hydrateFromConfiguration(array $applicationConfiguration)
    {
        // TODO implements language from symfony configuration
        $this->lang = 'fr';
        $this->layout = $applicationConfiguration['layout'];
        $this->blockTemplate = $applicationConfiguration['block_template'];
        $this->dateFormat = $applicationConfiguration['date_format'];

        if (array_key_exists('title', $applicationConfiguration)) {
            $this->title = $applicationConfiguration['title'];
        }
        if (array_key_exists('description', $applicationConfiguration)) {
            $this->description = $applicationConfiguration['description'];
        }
        if (array_key_exists('bootstrap', $applicationConfiguration)) {
            $this->bootstrap = $applicationConfiguration['bootstrap'];
        }
    }
}
