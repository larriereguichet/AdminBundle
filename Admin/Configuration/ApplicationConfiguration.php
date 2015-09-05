<?php

namespace BlueBear\AdminBundle\Admin\Configuration;

class ApplicationConfiguration
{
    protected $title;

    protected $lang;

    protected $layout;

    protected $blockTemplate;

    protected $description;

    protected $bootstrap = false;

    protected $dateFormat;

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

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return mixed
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param mixed $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return mixed
     */
    public function getBlockTemplate()
    {
        return $this->blockTemplate;
    }

    /**
     * @param mixed $blockTemplate
     */
    public function setBlockTemplate($blockTemplate)
    {
        $this->blockTemplate = $blockTemplate;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return boolean
     */
    public function useBootstrap()
    {
        return $this->bootstrap;
    }

    /**
     * @param boolean $bootstrap
     */
    public function setBootstrap($bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    /**
     * @return mixed
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @param mixed $dateFormat
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }
}
