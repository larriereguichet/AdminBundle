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
        $this->title = $applicationConfiguration['title'];
        $this->description = $applicationConfiguration['description'];
        $this->bootstrap = $applicationConfiguration['bootstrap'];
    }
}
