<?php

namespace BlueBear\AdminBundle\Admin;

use BlueBear\AdminBundle\Admin\Render\RendererInterface;

class Field
{
    const TYPE_STRING = 'string';
    const TYPE_LINK = 'link';
    const TYPE_ARRAY = 'array';
    const TYPE_DATE = 'date';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var RendererInterface
     */
    protected $renderer;

    public function __construct($name, RendererInterface $renderer)
    {
        $this->name = $name;
        $this->renderer = $renderer;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return RendererInterface
     */
    public function getRenderer()
    {
        return $this->renderer;
    }
}
